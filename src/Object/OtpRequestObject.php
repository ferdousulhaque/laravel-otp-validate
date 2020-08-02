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
        $data_for_validate = [
            'client_req_id' => $client_req_id,
            'number' => $number,
            'email' => $email,
            'type' => $type,
            'resend' => $resend
        ];

        $valid = self::validation($data_for_validate);
        if($valid){
            $this->client_req_id = $client_req_id;
            $this->number = $number;
            $this->email = $email;
            $this->type = $type;
            $this->resend = $resend;
        }else{
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
            'client_req_id' => 'required|max:255',
            'number' => 'required|numeric',
            'type' => 'required|max:50',
            'email' => 'nullable|email',
            'resend' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            return false;
        }
        return true;
    }
}


