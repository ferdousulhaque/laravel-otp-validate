<?php

namespace Ferdous\OtpValidator\Object;

use Illuminate\Support\Facades\Validator;

class OtpValidateRequestObject
{
    public $unique_id;
    public $otp;

    /**
     * OtpValidateRequestObject constructor.
     * @param string $unique_id
     * @param string $otp
     */
    public function __construct(string $unique_id, string $otp)
    {
        if(empty($unique_id) || empty($otp)) return null;
        $this->unique_id = $unique_id;
        $this->otp = $otp;
    }
}


