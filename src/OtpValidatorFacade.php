<?php

namespace Ferdous\OtpValidator;

use Illuminate\Support\Facades\Facade;

class OtpValidatorFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'otpvalidator';
    }
}
