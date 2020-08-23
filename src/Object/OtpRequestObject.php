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
     * @param string $number
     * @param string $type
     * @param string|null $email
     * @param int|null $resend
     */
    public function __construct(string $client_req_id, string $number, string $type, ?string $email=null, ?int $resend=0) {
            $this->client_req_id = $client_req_id;
            $this->number = $number;
            $this->email = $email;
            $this->type = $type;
            $this->resend = $resend;
    }
}


