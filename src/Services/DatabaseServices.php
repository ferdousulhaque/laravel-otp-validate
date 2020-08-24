<?php


namespace Ferdous\OtpValidator\Services;


use Ferdous\OtpValidator\Constants\DBStates;
use Ferdous\OtpValidator\Models\Otps;
use Ferdous\OtpValidator\Object\OtpRequestObject;
use Ferdous\OtpValidator\Object\OtpValidateRequestObject;
use Illuminate\Support\Carbon;

class DatabaseServices
{
    /**
     * @param OtpRequestObject $request
     * @param $otp
     * @param $uuid
     * @return mixed
     */
    public static function createOtpRecord(OtpRequestObject $request, $otp, $uuid){
        return Otps::create([
            'client_req_id' => $request->client_req_id,
            'number' => $request->number,
            'email' => $request->email,
            'type' => $request->type,
            'otp' => $otp,
            'uuid' => $uuid,
            'retry' => 0,
            'status' => DBStates::NEW
        ]);
    }

    /**
     * @param $uuid
     * @param $resend
     * @return mixed
     */
    public static function findUuidAvailable($uuid, $resend){
        if($resend == 0){
            return Otps::where('status', DBStates::NEW)
                ->where('uuid', $uuid)
                ->where('created_at', '>', Carbon::now(config('app.timezone'))->subSeconds(config('otp.timeout')))
                ->first();
        }
        return Otps::where('status', DBStates::NEW)
            ->where('uuid', $uuid)
            ->first();

    }

    /**
     * @param OtpRequestObject $request
     * @return mixed
     */
    public static function expireOld(OtpRequestObject $request){
        return Otps::where('client_req_id', $request->client_req_id)
            ->where('type', $request->type)
            ->where('status', 'new')
            ->update(['status' => 'expired']);
    }

    /**
     * @param OtpValidateRequestObject $request
     * @param $state
     * @return mixed
     */
    public static function updateTo(OtpValidateRequestObject $request, $state){
        return Otps::where('uuid', $request->unique_id)
            ->update(['status' => $state]);
    }

    /**
     * @param OtpValidateRequestObject $request
     * @return mixed
     */
    public static function updateRetry(OtpValidateRequestObject $request){
        return Otps::where('uuid', $request->unique_id)
            ->increment('retry');
    }

    public static function countResend(OtpRequestObject $request){
        return Otps::where('client_req_id', $request->client_req_id)
            ->where('type', $request->type)->count();
    }
}
