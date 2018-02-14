<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class People extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();
        Schema::create('people', function (Blueprint $table) {
            $table->increments('person_id');
            $table->string('person_first_name', 100)->nullable();
            $table->string('person_last_name', 100)->nullable();
            $table->integer('user_id')->unsigned();

            $table->foreign('user_id')
                ->references('user_id')->on('users')
                ->onDelete('cascade');
            $table->index(['user_id', 'person_id'], 'idx_user_person');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('people');
    }
}
