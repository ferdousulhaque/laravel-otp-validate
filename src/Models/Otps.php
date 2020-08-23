<?php

namespace Ferdous\OtpValidator\Models;

use Illuminate\Database\Eloquent\Model;

class Otps extends Model
{
    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        $this->setTable(config('otp.table-name'));
        parent::__construct($attributes);
    }

    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }
}
