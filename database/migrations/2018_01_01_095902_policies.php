<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Policies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('policy_actions', function (Blueprint $table) {
            $table->increments('policy_action_id');

            $table->string('policy_action_name',75);
        });

        Schema::create('policy_targets', function (Blueprint $table) {
            $table->increments('policy_target_id');

            $table->integer('entity_type_id')->unsigned();

            $table->foreign('entity_type_id')
                ->references('entity_type_id')->on('entity_types')
                ->onDelete('cascade');
        });

        Schema::create('policies', function (Blueprint $table) {
            $table->increments('policy_id');

            $table->text('policy_description');
            //add
            $table->integer('policy_action_id')->unsigned();
            //users
            $table->integer('entity_id')->unsigned();
            //the user that can add users is user #1
            $table->integer('entity_type_id')->unsigned();

            //the user that can update users can update all of them -> policy target id is 0
            //the user that can update users can update all that are not admins
            //the user that can update users can update all of 5,6,7,8,9
            //user that can update posts can update all of them except those written by 1,2,3,4,5
            $table->integer('policy_target_id')->unsigned()->default(0);

            $table->foreign('policy_action_id')
                ->references('policy_action_id')->on('policy_actions');
            $table->foreign('entity_type_id')
                ->references('entity_type_id')->on('entity_types');
            $table->foreign('entity_id')
                ->references('entity_id')->on('entities');
        });

        Schema::create('policy_controls', function (Blueprint $table) {
            $table->increments('policy_control_id');

            //the user 254 is allowed to update users in his group
            $table->integer('policy_id')->unsigned();

            //group 12 is allowed to interact with the policy above
            $table->integer('entity_type_id')->unsigned();

            //in fact, they're allowed to view it only
            $table->integer('policy_action_id')->unsigned();

        });



    }
}
