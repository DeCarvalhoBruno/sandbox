<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Blog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_post_status', function (Blueprint $table) {
            $table->increments('blog_post_status_id');

            $table->string('blog_post_status_name',20)->nullable();
        });

        Schema::create('blog_posts', function (Blueprint $table) {
            $table->increments('blog_post_id');

            $table->unsignedInteger('user_id');
            $table->unsignedInteger('blog_post_status_id')->default(\App\Models\Media\BlogPostStatus::BLOG_POST_STATUS_DRAFT);

            $table->string('blog_post_title')->nullable();
            $table->text('blog_post_content')->nullable();
            $table->text('blog_post_excerpt')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('user_id')->on('users');
        });

        $status = [
            [
                'blog_post_status_name' => 'DRAFT'
            ],
            [
                'blog_post_status_name' => 'REVIEW'
            ],
            [
                'blog_post_status_name' => 'PUBLISHED'
            ]
        ];
        \App\Models\Media\BlogPostStatus::insert($status);


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
