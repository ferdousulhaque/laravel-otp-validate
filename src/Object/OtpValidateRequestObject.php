<?php

namespace Ferdous\OtpValidator\Object;

use Illuminate\Support\Facades\Validator;

class OtpValidateRequestObject
{
    public $unique_id;
    public $otp;

    /**
     * OtpValidateRequestObject constructor.
     * @param int $unique_id
     * @param string $otp
     */
    public function __construct(int $unique_id, string $otp)
    {
        $data_for_validate = [
            'unique_id' => $unique_id,
            'otp' => $otp
        ];

        $valid = self::validation($data_for_validate);
        if ($valid) {
            $this->unique_id = $unique_id;
            $this->otp = $otp;
        } else {
            //TODO: Need to Give Something
        }
    }

    /**
     * @param $data
     * @return bool
     */
    private function validation($data)
    {
        $validator = Validator::make($data, [
            'unique_id' => 'required|numeric',
            'otp' => 'required'
        ]);
        if ($validator->fails()) {
            return false;
        }
        return true;
    }
}


