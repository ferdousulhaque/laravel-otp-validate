<?php


namespace Ferdous\OtpValidator;

use Ferdous\OtpValidator\Constants\DBStates;
use Ferdous\OtpValidator\Constants\StatusCodes;
use Ferdous\OtpValidator\Constants\StatusMessages;
use Ferdous\OtpValidator\Object\OtpRequestObject;
use Ferdous\OtpValidator\Object\OtpValidateRequestObject;
use Ferdous\OtpValidator\Services\OtpService;
use Ferdous\OtpValidator\Services\Responder;
use Ferdous\OtpValidator\Services\Transporter;


class OtpValidator
{

    public static $switch = [
        'enabled' => 1,
        'disabled' => 0
    ];

    const ENABLED = 1;
    const DISABLED = 0;

    /**
     * @param OtpRequestObject $request
     * @return array
     */
    public static function requestOtp(OtpRequestObject $request)
    {
        if (self::$switch[config('otp.service')] !== self::ENABLED) {
            return Responder::formatter([
                'code' => StatusCodes::SERVICE_UNAVAILABLE,
                'message' => StatusMessages::SERVICE_UNAVAILABLE
            ]);
        }

        if (empty($request)) {
            return Responder::formatter([
                'code' => StatusCodes::BAD_REQUEST,
                'message' => StatusMessages::BAD_REQUEST
            ]);
        }

        try {
            $getId = self::getUuidId($request);
        } catch (\Throwable $th) {
            //throw $th;
            return Responder::formatter([
                'code' => StatusCodes::BAD_REQUEST,
                'message' => StatusMessages::BAD_REQUEST
            ]);
        }

        if(empty($getId))
            return Responder::formatter([
                'code' => StatusCodes::RESEND_SERVICE_DISABLED,
                'message' => StatusMessages::RESEND_SERVICE_DISABLED
            ]);

        return Responder::formatter([
            'code' => StatusCodes::SUCCESSFULLY_SENT_OTP,
            'message' => StatusMessages::SUCCESSFULLY_SENT_OTP,
            'uniqueId' => $getId,
            'type' => $request->type
        ]);
    }

    /**
     * @param OtpRequestObject $request
     * @return string
     */
    private static function getUuidId(OtpRequestObject $request): string
    {
        try {
            $data = OtpService::expireOldOtpRequests($request);

            if(self::$switch[config('otp.resend')] === self::DISABLED && !empty($data)) return "";

            // Resend Exceed
            if(self::$switch[config('otp.resend')] === self::ENABLED && OtpService::countResend($request) > config('otp.max-resend')) return "";

            // OTP Generation and Persistence
            $getOtp = OtpService::otpGenerator();
            $uuid = md5($request->client_req_id.time());

            // Send OTP
            Transporter::sendCode($request, $getOtp);

            OtpService::createOtpRecord($request, $getOtp, $uuid);
            return $uuid;
        } catch (\Exception $ex) {
            throw $ex;
            // return $ex->getMessage();
        }
    }

    /**
     * @param OtpValidateRequestObject $request
     * @return array
     */
    public static function validateOtp(OtpValidateRequestObject $request): array
    {
        if(empty($request)){
            return Responder::formatter([
                'code' => StatusCodes::BAD_REQUEST,
                'message' => StatusMessages::BAD_REQUEST
            ]);
        }
        $getData = OtpService::findRequest($request->unique_id);

        if (!empty($getData)) {
            if ($getData->otp == $request->otp) {
                OtpService::updateTo($request, DBStates::USED);
                return Responder::formatter([
                    'code' => StatusCodes::OTP_VERIFIED,
                    'message' => StatusMessages::VERIFIED_OTP,
                    'requestId' => $getData->client_req_id,
                    'type' => $getData->type
                ]);
            } else {
                if ($getData->retry > config('otp.max-retry')) {
                    OtpService::updateTo($request, DBStates::EXPIRED);
                    return Responder::formatter([
                        'code' => StatusCodes::TOO_MANY_WRONG_RETRY,
                        'message' => StatusMessages::TOO_MANY_WRONG_RETRY,
                        'resendId' => $request->unique_id
                    ]);
                } else {
                    OtpService::updateRetry($request);
                    return Responder::formatter([
                        'code' => StatusCodes::INVALID_OTP_GIVEN,
                        'message' => StatusMessages::INVALID_OTP_GIVEN,
                        'resendId' => $request->unique_id
                    ]);
                }
            }
        } else {
            return Responder::formatter([
                'code' => StatusCodes::OTP_TIMEOUT,
                'message' => StatusMessages::OTP_TIMEOUT,
                'resendId' => $request->unique_id
            ]);
        }
    }

    /**
     * @param $uniqueId
     * @return array
     */
    public static function resendOtp($uniqueId)
    {
        try {
            $request_data = OtpService::findRequest($uniqueId, 1);

            if (!empty($request_data) && self::$switch[config('otp.resend')] === self::ENABLED) {
                return self::requestOtp(
                    new OtpRequestObject(
                        $request_data->client_req_id,
                        $request_data->type,
                        $request_data->number,
                        $request_data->email
                    )
                );
            }
            return [];
        } catch (\Exception $ex) {
            return [];
        }
    }
}
