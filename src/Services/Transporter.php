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
        try{
            if (intval(config('otp.send-by.email')) === 1) {
                $email = new EmailTransportService($request->email, $otp);
                $email->send();
            }
        }catch (\Exception $ex){
            dd($ex->getMessage());
        }

        try{
            if (intval(config('otp.send-by.sms')) === 1) {
                $sms = new SMSTransportService($request->number, $otp);
                $sms->send();
            }
        }catch (\Exception $ex){
            dd($ex->getMessage());
        }

    }
}
