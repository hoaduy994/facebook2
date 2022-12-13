<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('number_phone')->unique()->nullable();
            $table->tinyInteger('confirm')->nullable();
            $table->string('confirmation_code')->nullable();
            $table->dateTime('confirmation_code_expired_in')->nullable();
            $table->rememberToken();
            $table->tinyInteger('gender')->nullable();
            $table->string('avatar')->nullable();
            $table->string('background_img')->nullable();
            $table->string('bio')->nullable();
            $table->string('address')->nullable();
            $table->date('birthday')->format('Y-m-d')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
