<?php

namespace Ferdous\OtpValidator\Services;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->from(config('otp.email.from'),config('otp.email.name'))
            ->view('vendor.template-otp.email')
            ->subject(config('otp.email.subject'));
    }
}
