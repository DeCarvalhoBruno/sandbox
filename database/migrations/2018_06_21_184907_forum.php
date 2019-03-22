<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Forum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forum_boards', function (Blueprint $table) {
            $table->increments('forum_board_id');
            $table->unsignedInteger('language_id');

            $table->string('forum_board_name')->nullable();
            $table->string('forum_board_slug')->nullable();

            $table->index(array('forum_board_id', 'forum_board_slug', 'language_id'), 'idx_board_language');
            $table->foreign('language_id')
                ->references('language_id')->on('languages')
                ->onDelete('cascade');
        });

        Schema::create('forum_threads', function (Blueprint $table) {
            $table->increments('forum_thread_id');
            $table->unsignedInteger('forum_board_id');
            $table->unsignedInteger('forum_thread_first_post_id');
            $table->unsignedInteger('thread_user_id');

            $table->string('forum_thread_title')->nullable();
            $table->dateTime('forum_thread_last_post');

            $table->timestamps();
            $table->index(array('forum_thread_id', 'thread_user_id'), 'idx_thread_user');

            $table->foreign('forum_board_id')
                ->references('forum_board_id')->on('forum_boards')
                ->onDelete('cascade');
            $table->foreign('thread_user_id')
                ->references('user_id')->on('users');
        });

        Schema::create('forum_posts', function (Blueprint $table) {
            $table->increments('forum_post_id');
            $table->unsignedInteger('entity_type_id');
            $table->unsignedInteger('post_user_id');

            $table->text('forum_post')->nullable();

            $table->timestamps();
            $table->index(array('forum_post_id', 'post_user_id'), 'idx_post_user');

            $table->foreign('entity_type_id')
                ->references('entity_type_id')->on('entity_types')
                ->onDelete('cascade');
            $table->foreign('post_user_id')
                ->references('user_id')->on('users');
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
