<?php

namespace Ferdous\OtpValidator\Services;

use Illuminate\Support\Facades\Mail;

class EmailTransportService implements TransportServiceInterface
{
    private $email;
    private $otp;

    public function __construct($email, $otp)
    {
        $this->email = $email;
        $this->otp = $otp;
    }

    public function send()
    {
        $mailableClass = config('otp.email.mailable-class', OtpMailable::class);
        if (!empty($this->email) && !empty($this->otp)) {
            Mail::to($this->email)->send(new $mailableClass($this->otp));
        }
    }

}
