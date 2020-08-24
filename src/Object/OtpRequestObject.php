<?php

namespace Ferdous\OtpValidator\Object;

use Illuminate\Support\Facades\Validator;

class OtpRequestObject
{
    public $client_req_id;
    public $number;
    public $email;
    public $type;
    public $resend;

    /**
     * OtpRequestObject constructor.
     * @param string $client_req_id
     * @param string $type
     * @param string|null $number
     * @param string|null $email
     * @param int|null $resend
     */
    public function __construct(string $client_req_id, string $type, ?string $number=null, ?string $email=null, ?int $resend=0) {
        if(intval(config('otp.send-by.email')) === 1 && empty($email)) return null;
        if(intval(config('otp.send-by.sms')) === 1 && empty($number)) return null;

        $this->client_req_id = $client_req_id;
        $this->number = $number;
        $this->email = $email;
        $this->type = $type;
        $this->resend = $resend;
    }
}


