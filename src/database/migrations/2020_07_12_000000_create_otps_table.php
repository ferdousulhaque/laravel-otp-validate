<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('otp.table-name'), function (Blueprint $table) {
            $table->id();
            $table->string('client_req_id');
            $table->string('number')->nullable();
            $table->string('email')->nullable();
            $table->string('type');
            $table->string('otp');
            $table->string('uuid');
            $table->tinyInteger('retry');
            $table->enum('status',['new','used', 'expired']);
            $table->timestamps();
            $table->index(['client_req_id', 'uuid', 'status', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('otp.table-name'));
    }
}
