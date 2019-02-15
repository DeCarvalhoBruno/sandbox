<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_events', function (Blueprint $table) {
            $table->increments('email_event_id');

            $table->unsignedSmallInteger('entity_id');
            $table->string('email_event_name', 70)->nullable();
            $table->foreign('entity_id')
                ->references('entity_id')->on('entities')
                ->onDelete('cascade');
        });

        Schema::create('email_subscribers', function (Blueprint $table) {
            $table->increments('email_subscriber_id');

            $table->unsignedInteger('email_subscriber_target_id')->default(0);
//            $table->string('email')->unique()->nullable();
            $table->unsignedInteger('email_event_id');

            $table->foreign('email_subscriber_target_id')
                ->references('entity_type_id')->on('entity_types')
                ->onDelete('cascade');
            $table->foreign('email_event_id')
                ->references('email_event_id')->on('email_events')
                ->onDelete('cascade');
        });

        Schema::create('email_schedules', function (Blueprint $table) {
            $table->increments('email_schedule_id');

            $table->unsignedInteger('email_schedule_source_id');
//            $table->unsignedInteger('email_schedule_target_id');
            $table->unsignedInteger('email_event_id');
            $table->string('email_schedule_name', 100)->nullable();

            $table->date('email_schedule_send_at')->nullable();
            $table->unsignedSmallInteger('email_schedule_periodicity')->nullable()->default(0);

//            $table->foreign('email_schedule_target_id')
//                ->references('entity_type_id')->on('entity_types')
//                ->onDelete('cascade');
            $table->foreign('email_schedule_source_id')
                ->references('entity_type_id')->on('entity_types')
                ->onDelete('cascade');
            $table->foreign('email_event_id')
                ->references('email_event_id')->on('email_events')
                ->onDelete('cascade');
//            $table->unique(['email_schedule_target_id', 'email_schedule_source_id', 'email_event_id'],
//                'idx_email_schedule');
        });

        Schema::create('email_campaigns', function (Blueprint $table) {
            $table->increments('email_campaign_id');

            $table->unsignedInteger('email_event_id');
            $table->string('email_campaign_name', 100)->nullable();

            $table->foreign('email_event_id')
                ->references('email_event_id')->on('email_events');
        });

        Schema::create('email_user_event_types', function (Blueprint $table) {
            $table->increments('email_user_event_type_id');
            $table->string('email_user_event_type_name', 100);
        });

        Schema::create('email_user_events', function (Blueprint $table) {
            $table->increments('email_user_event_id');
            $table->unsignedInteger('email_user_event_type_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();

            $table->foreign('email_user_event_type_id')
                ->references('email_user_event_type_id')->on('email_user_event_types');
        });

        Schema::create('email_user_event_clicks', function (Blueprint $table) {
            $table->increments('email_user_click_id');
            $table->unsignedInteger('email_user_event_id');

            $table->string('email_user_click_url')->nullable();
            $table->string('email_user_click_entity', 50)->nullable();
            $table->unsignedInteger('entity_id')->nullable();

            $table->foreign('email_user_event_id')
                ->references('email_user_event_id')->on('email_user_events')
                ->onDelete('cascade');
        });

        Schema::create('email_user_event_fails', function (Blueprint $table) {
            $table->increments('email_user_fail_id');
            $table->unsignedInteger('email_user_event_id');

            $table->string('email_user_fail_message')->nullable();
            $table->string('email_user_fail_code', 50)->nullable();
            $table->string('email_user_fail_description')->nullable();
            $table->timestamp('email_user_fail_timestamp')->nullable();

            $table->foreign('email_user_event_id')
                ->references('email_user_event_id')->on('email_user_events')
                ->onDelete('cascade');
        });

        \App\Models\Email\EmailUserEventType::insert([
                [
                    'email_user_event_type_id' => \App\Models\Email\EmailUserEventType::OPENED,
                    'email_user_event_type_name' => 'opened'
                ],
                [
                    'email_user_event_type_id' => \App\Models\Email\EmailUserEventType::CLICKED,
                    'email_user_event_type_name' => 'clicked'
                ],
                [
                    'email_user_event_type_id' => \App\Models\Email\EmailUserEventType::COMPLAINED,
                    'email_user_event_type_name' => 'complained'
                ],
                [
                    'email_user_event_type_id' => \App\Models\Email\EmailUserEventType::FAILED,
                    'email_user_event_type_name' => 'failed'
                ]
            ]
        );

        $entities = \App\Models\Email\EmailEvent::getConstants();

        $systemEntities = [
            'NEWSLETTER' => \App\Models\Entity::SYSTEM,
        ];
        $emailEvents = [];
        foreach ($entities as $name => $id) {
            $emailEvents[] = [
                'email_event_id'=>$id,
                'email_event_name' => $name,
                'entity_id' => $systemEntities[$name]
            ];
        }
        \App\Models\Email\EmailEvent::insert($emailEvents);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

}
