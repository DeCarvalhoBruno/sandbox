<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Users extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();
        Schema::create('users', function (Blueprint $table) {
            $table->increments('user_id');
            $table->string('email')->unique()->nullable();
            $table->string('username',15)->unique()->nullable();
            $table->string('password')->nullable();
            $table->boolean('activated')->default(false);
            $table->rememberToken()->nullable();
            $table->index('remember_token','idx_users_remember_token');
        });

        Schema::create('user_activations', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->string('activation_token',32);

            $table->foreign('user_id')
                ->references('user_id')->on('users')
                ->onDelete('cascade');

            $table->index(['user_id','activation_token'],'idx_users_remember_token');
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
