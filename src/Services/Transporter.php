<?php


namespace Ferdous\OtpValidator\Services;


use Ferdous\OtpValidator\Object\OtpRequestObject;

class Transporter
{
    /**
     * @param OtpRequestObject $request
     * @param string $otp
     */
    public static function sendCode(OtpRequestObject $request, string $otp)
    {
        try {
            self::sendOverEmail($request, $otp);
            self::sendOverSMS($request, $otp);
            self::sendOverAwsSns($request, $otp);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function sendOverEmail(OtpRequestObject $request, string $otp)
    {
        try {
            if (intval(config('otp.send-by.email')) === 1 && !empty($request->email)) {
                self::sendOver(new EmailTransportService($request->email, $otp));
            }
        } catch (\Exception $ex) {
            // return false;
            throw $ex;
            //dd($ex->getMessage());
        }
    }

    public static function sendOverSMS(OtpRequestObject $request, string $otp)
    {
        try {
            if (intval(config('otp.send-by.sms')) === 1 && !empty($request->number)) {
                self::sendOver(new SMSTransportService($request->number, $otp));
            }
        } catch (\Exception $ex) {
            throw $ex;
            // return false;
            //dd($ex->getMessage());
        }
    }

    public static function sendOverAwsSns(OtpRequestObject $request, string $otp)
    {
        try {
            if (intval(config('otp.send-by.aws-sns')) === 1 && !empty($request->number)) {
                self::sendOver(new SNSTransportService($request->number, $otp));
            }
        } catch (\Exception $ex) {
            throw $ex;
            // return false;
            //dd($ex->getMessage());
        }
    }

    public static function sendOver(TransportServiceInterface $service)
    {
        try {
            $service->send();
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
