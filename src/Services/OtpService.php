<?php


namespace Ferdous\OtpValidator\Services;


use Ferdous\OtpValidator\Constants\DBStates;
use Ferdous\OtpValidator\Object\OtpRequestObject;
use Ferdous\OtpValidator\Object\OtpValidateRequestObject;

class OtpService
{
    /**
     * @param int $digit
     * @return string
     */
    public static function otpGenerator(int $digit = 4)
    {
        $gen = '0135792468';
        $res = '';
        for ($i = 1; $i <= $digit; $i++)
        {
            $res .= substr($gen, (rand()%(strlen($gen))), 1);
        }
        if(isset($res[0]) && $res[0] == '0'){
            $res[0] = substr(trim($gen,'0'), (rand()%(strlen($gen)-1)), 1);
        }
        return $res;
    }

    /**
     * @param OtpRequestObject $request
     * @param $otp
     * @param $uuid
     * @return mixed
     */
    public static function createOtpRecord(OtpRequestObject $request, $otp, $uuid)
    {
        return DatabaseServices::createOtpRecord($request, $otp, $uuid);
    }

    /**
     * @param $uuid
     * @param int $resend
     * @return mixed
     */
    public static function findRequest($uuid, $resend = 0){
        return DatabaseServices::findUuidAvailable($uuid, $resend);
    }

    /**
     * @param OtpRequestObject $request
     * @return mixed
     */
    public static function expireOldOtpRequests(OtpRequestObject $request){
        return DatabaseServices::expireOld($request);
    }

    /**
     * @param OtpValidateRequestObject $request
     * @param $state
     */
    public static function updateTo(OtpValidateRequestObject $request, $state): void{
        DatabaseServices::updateTo($request, $state);
    }

    /**
     * @param OtpValidateRequestObject $request
     */
    public static function updateRetry(OtpValidateRequestObject $request): void{
        DatabaseServices::updateRetry($request);
    }

    public static function countResend(OtpRequestObject $request){
        return DatabaseServices::countResend($request);
    }
}
