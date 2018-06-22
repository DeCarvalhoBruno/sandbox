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

            $table->string('blog_post_status_name', 20)->nullable();
        });

        Schema::create('blog_posts', function (Blueprint $table) {
            $table->increments('blog_post_id');

            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedInteger('blog_post_status_id')
                ->default(\App\Models\Blog\BlogPostStatus::BLOG_POST_STATUS_DRAFT);

            $table->string('blog_post_title')->nullable();
            $table->text('blog_post_content')->nullable();
            $table->text('blog_post_excerpt')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('user_id')->on('users');
        });

        Schema::create('blog_post_version', function (Blueprint $table) {
            $table->increments('blog_post_version_id');

            $table->unsignedInteger('blog_post_id');
            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedTinyInteger('blog_post_version_number')->default(0);
            $table->text('blog_post_content')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('blog_post_id')
                ->references('blog_post_id')->on('blog_posts')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('user_id')->on('users');
        });

        Schema::create('blog_post_categories', function (Blueprint $table) {
            $table->increments('blog_post_category_id');

            $table->unsignedInteger('parent_id')->nullable();
            $table->unsignedInteger('lft')->default(0);
            $table->unsignedInteger('rgt')->default(0);
            $table->string('blog_post_category_name', 75)->nullable();

            $table->index(array('lft', 'rgt', 'parent_id'));
        });

        Schema::create('blog_post_tags', function (Blueprint $table) {
            $table->increments('blog_post_tag_id');

            $table->string('blog_post_tag_name', 75)->nullable();
        });

        Schema::create('blog_post_tag_records', function (Blueprint $table) {
            $table->increments('blog_post_tag_record_id');

            $table->unsignedInteger('blog_post_id');
            $table->unsignedInteger('blog_post_tag_id');

            $table->foreign('blog_post_id')
                ->references('blog_post_id')->on('blog_posts')
                ->onDelete('cascade');
            $table->foreign('blog_post_tag_id')
                ->references('blog_post_tag_id')->on('blog_post_tags')
                ->onDelete('cascade');
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
        \App\Models\Blog\BlogPostStatus::insert($status);


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
