<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SystemEventLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_events', function (Blueprint $table) {
            $table->increments('system_event_id');
            $table->string('system_event_name', 75);
        });

        Schema::create('system_event_log', function (Blueprint $table) {
            $table->increments('system_event_log_id');

            $table->unsignedInteger('system_event_id');
            $table->text('system_event_log_data')->nullable();
            $table->dateTime('created_at')->nullable();

            $table->foreign('system_event_id')
                ->references('system_event_id')->on('system_events')
                ->onDelete('cascade');
        });

        \App\Models\System\SystemEvent::insert([
            ['system_event_id' => 1, 'system_event_name' => 'Newsletter Subscription'],
            ['system_event_id' => 2, 'system_event_name' => 'Contact form message'],
        ]);

        Schema::create('system_sections', function (Blueprint $table) {
            $table->increments('system_section_id');
            $table->string('system_section_name', 75);
        });

        \App\Models\System\SystemSection::insert([
            ['system_section_name' => 'Backend'],
            ['system_section_name' => 'Frontend'],
        ]);

        Schema::create('system_user_settings', function (Blueprint $table) {
            $table->increments('system_user_setting_id');

            $table->unsignedInteger('system_section_id');
            $table->unsignedInteger('user_id');
            $table->string('system_events_subscribed', 75);

            $table->foreign('system_section_id')
                ->references('system_section_id')->on('system_sections')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('user_id')->on('users')
                ->onDelete('cascade');
        });
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
