<?php


namespace Ferdous\OtpValidator\Services;


use Ferdous\OtpValidator\Constants\DBStates;
use Ferdous\OtpValidator\Object\OtpRequestObject;
use Ferdous\OtpValidator\Object\OtpValidateRequestObject;

class OtpService
{
    /**
     * @param int $defaultDigit
     * @return int
     */
    public static function otpGenerator(int $defaultDigit = 4)
    {
        $digit = config('otp.digit') ?? $defaultDigit;
        return rand(pow(10, $digit - 1), pow(10, $digit) - 1);
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
