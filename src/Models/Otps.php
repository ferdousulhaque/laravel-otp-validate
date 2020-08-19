<?php

namespace Ferdous\OtpValidator\Models;

use Illuminate\Database\Eloquent\Model;

class Otps extends Model
{
    protected $guarded = [];

    public function __construct()
    {
        $this->setTable(config('otp.table-name'));
    }
}
