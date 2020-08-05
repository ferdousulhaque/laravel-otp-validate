<?php


namespace Ferdous\OtpValidator;

use Ferdous\OtpValidator\Models\Otps;
use Ferdous\OtpValidator\Object\OtpRequestObject;
use Ferdous\OtpValidator\Object\OtpValidateRequestObject;
use Ferdous\OtpValidator\Services\EmailService;
use Ferdous\OtpValidator\Services\SMSService;
use Illuminate\Support\Carbon;
use Ramsey\Uuid\Uuid;

class OtpValidator
{

    public static $switch = [
        'enabled' => 1,
        'disabled' => 0
    ];

    /**
     * @param OtpRequestObject $request
     * @return array
     */
    public static function requestOtp(OtpRequestObject $request)
    {
        if (!empty($request->client_req_id)) {
            if (self::$switch[config('otp.service')] === 1) {
                $getId = self::getOtpId($request);

                if (!empty($getId)) {
                    return [
                        'code' => 201,
                        'status' => true,
                        'uniqueId' => $getId,
                        'type' => $request->type
                    ];
                } else {
                    return [
                        'code' => 503,
                        'status' => 'Resend Service is disabled'
                    ];
                }

            } else {
                return [
                    'code' => 503,
                    'status' => 'Service Unavailable'
                ];
            }
        } else {
            return [
                'code' => 400,
                'status' => 'Bad Request'
            ];
        }
    }

    /**
     * @param OtpValidateRequestObject $request
     * @return array
     */
    public static function validateOtp(OtpValidateRequestObject $request): array
    {
        $getOtpData = Otps::where('status', 'new')
            ->where('uuid', $request->unique_id)
            ->where('created_at', '>', Carbon::now(config('app.timezone'))->subSeconds(config('otp.timeout')))
            ->first();

        if (!empty($getOtpData)) {
            if ($getOtpData->otp == $request->otp) {
                Otps::where('uuid', $request->unique_id)
                    ->update(['status' => 'used']);
                return [
                    'code' => 200,
                    'status' => true,
                    'requestId' => $getOtpData->client_req_id,
                    'type' => $getOtpData->type
                ];
            } else {
                if ($getOtpData->retry > config('otp.max-retry')) {
                    Otps::where('uuid', $request->unique_id)
                        ->update(['status' => 'expired']);
                    return [
                        'code' => 413,
                        'status' => false,
                        'resendId' => $request->unique_id,
                        'error' => 'too many wrong try'
                    ];
                } else {
                    Otps::where('uuid', $request->unique_id)
                        ->increment('retry');
                    return [
                        'code' => 400,
                        'status' => false,
                        'resendId' => $request->unique_id,
                        'error' => 'invalid otp'
                    ];
                }
            }
        } else {
            return [
                'code' => 404,
                'status' => false,
                'resendId' => $request->unique_id,
                'error' => 'otp expired/timeout'
            ];
        }
    }

    /**
     * @param int $defaultDigit
     * @return int
     */
    private static function randomOtpGen(int $defaultDigit = 4)
    {
        $digit = config('otp.digit') ?? $defaultDigit;
        return rand(pow(10, $digit - 1), pow(10, $digit) - 1);
    }

    /**
     * @param OtpRequestObject $request
     * @return string
     */
    private static function getOtpId(OtpRequestObject $request): string
    {
        try {
            $count = Otps::where('number', $request->number)
                ->where('type', $request->type)
                ->where('status', 'new')
                ->update(['status' => 'expired']);

            if (self::$switch[config('otp.resend')] === 0 && intval($count) === 1) {
                return "";
            }

            $getOtp = self::randomOtpGen();
            $uuid = md5($request->client_req_id.time());
            $otp_request = Otps::create([
                'client_req_id' => $request->client_req_id,
                'number' => $request->number,
                'email' => $request->email,
                'type' => $request->type,
                'otp' => $getOtp,
                'uuid' => $uuid,
                'retry' => 0,
                'status' => 'new'
            ]);

            self::sendCode($request, $getOtp);

            return $otp_request->uuid;
        } catch (\Exception $ex) {
            dd($ex);
            return "";
        }
    }

    /**
     * @param OtpRequestObject $request
     * @param string $otp
     */
    private static function sendCode(OtpRequestObject $request, string $otp)
    {
        try{
            if (intval(config('otp.send-by.email')) === 1) {
                $email = new EmailService($request->email, $otp);
                $email->send();
            }
            if (intval(config('otp.send-by.sms')) === 1) {
                $sms = new SMSService($request->number, $otp);
                $sms->send();
            }
        }catch (\Exception $ex){
            dd($ex->getMessage());
        }

    }

    /**
     * @param $uniqueId
     * @return array|int
     */
    public static function resendOtp($uniqueId)
    {
        try {
            $request_data = Otps::where('uuid', $uniqueId)
                ->where('status', 'new')->first();

            if (!empty($request_data) && self::$switch[config('otp.resend')] === 1) {
                return self::requestOtp(
                    new OtpRequestObject(
                        $request_data->client_req_id,
                        $request_data->number,
                        $request_data->type,
                        $request_data->email
                    )
                );
            }
            return 0;
        } catch (\Exception $ex) {
            return 0;
        }
    }
}
