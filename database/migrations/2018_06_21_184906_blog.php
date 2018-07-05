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

            $table->string('blog_post_status_name', 75)->nullable();
        });

        Schema::create('blog_posts', function (Blueprint $table) {
            $table->increments('blog_post_id');

            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedInteger('blog_post_status_id')
                ->default(\App\Models\Blog\BlogPostStatus::BLOG_POST_STATUS_DRAFT);

            $table->string('blog_post_title')->nullable();
            $table->string('blog_post_slug')->nullable();
            $table->text('blog_post_content')->nullable();
            $table->text('blog_post_excerpt')->nullable();
            $table->boolean('blog_post_is_sticky')->default(false);
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

        Schema::create('blog_post_labels', function (Blueprint $table) {
            $table->increments('blog_post_label_id');

            $table->string('blog_post_label_name', 75)->nullable();
        });

        Schema::create('blog_post_label_types', function (Blueprint $table) {
            $table->increments('blog_post_label_type_id');

            $table->unsignedInteger('blog_post_label_id')
                ->default(\App\Models\Blog\BlogPostLabel::BLOG_POST_TAG);
            $table->foreign('blog_post_label_id')
                ->references('blog_post_label_id')->on('blog_post_labels')
                ->onDelete('cascade');
            $table->index(array('blog_post_label_type_id', 'blog_post_label_id'), 'idx_blog_post_labels');
        });

        Schema::create('blog_post_categories', function (Blueprint $table) {
            $table->increments('blog_post_category_id');

            $table->unsignedInteger('parent_id')->nullable();
            $table->unsignedInteger('lft')->default(0);
            $table->unsignedInteger('rgt')->default(0);
            $table->unsignedInteger('blog_post_label_type_id');
            $table->string('blog_post_category_name', 75)->nullable();
            $table->string('blog_post_category_slug', 75)->nullable();
            $table->string('blog_post_category_codename', 32)->nullable();

            $table->index(array('lft', 'rgt', 'parent_id'));
            $table->index(array('blog_post_category_codename'));

            $table->foreign('blog_post_label_type_id')
                ->references('blog_post_label_type_id')->on('blog_post_label_types')
                ->onDelete('cascade');
        });

        Schema::create('blog_post_tags', function (Blueprint $table) {
            $table->increments('blog_post_tag_id');

            $table->unsignedInteger('blog_post_label_type_id');
            $table->string('blog_post_tag_name', 35)->nullable();
            $table->string('blog_post_tag_slug', 35)->nullable();

            $table->foreign('blog_post_label_type_id')
                ->references('blog_post_label_type_id')->on('blog_post_label_types')
                ->onDelete('cascade');
            $table->unique(array('blog_post_tag_name'));
        });

        Schema::create('blog_post_label_records', function (Blueprint $table) {
            $table->increments('blog_post_label_record_id');

            $table->unsignedInteger('blog_post_id');
            $table->unsignedInteger('blog_post_label_type_id');

            $table->foreign('blog_post_id')
                ->references('blog_post_id')->on('blog_posts')
                ->onDelete('cascade');
            $table->foreign('blog_post_label_type_id')
                ->references('blog_post_label_type_id')->on('blog_post_label_types')
                ->onDelete('cascade');
            $table->index(array('blog_post_id', 'blog_post_label_type_id'), 'idx_blog_post_type');
        });

        $label_types = [
            ['blog_post_label_name' => \App\Models\Blog\BlogPostLabel::getConstantByID(\App\Models\Blog\BlogPostLabel::BLOG_POST_TAG)],
            ['blog_post_label_name' => \App\Models\Blog\BlogPostLabel::getConstantByID(\App\Models\Blog\BlogPostLabel::BLOG_POST_CATEGORY)],
        ];
        \App\Models\Blog\BlogPostLabel::insert($label_types);

        $status = [
            [
                'blog_post_status_name' => \App\Models\Blog\BlogPostStatus::getConstantByID(
                    \App\Models\Blog\BlogPostStatus::BLOG_POST_STATUS_DRAFT
                )
            ],
            [
                'blog_post_status_name' => \App\Models\Blog\BlogPostStatus::getConstantByID(
                    \App\Models\Blog\BlogPostStatus::BLOG_POST_STATUS_REVIEW
                )
            ],
            [
                'blog_post_status_name' => \App\Models\Blog\BlogPostStatus::getConstantByID(
                    \App\Models\Blog\BlogPostStatus::BLOG_POST_STATUS_PUBLISHED
                )
            ]
        ];
        \App\Models\Blog\BlogPostStatus::insert($status);

        $newLabelType = \App\Models\Blog\BlogPostLabelType::create(
            ['blog_post_label_id'=>\App\Models\Blog\BlogPostLabel::BLOG_POST_CATEGORY]
        );
        \App\Models\Blog\BlogPostCategory::create([
            'blog_post_category_name' => 'Default',
            'blog_post_category_codename' => makeHexUuid(),
            'blog_post_category_slug' => 'default',
            'blog_post_label_type_id' => $newLabelType->getKey()

        ]);
        $this->createViews();
    }

    public function createViews()
    {
        \DB::unprepared('CREATE VIEW blog_post_category_tree AS
        SELECT
            node.blog_post_category_id,
            node.blog_post_category_name as label,
            (COUNT(parent.blog_post_category_id) - 1) AS lvl,
            node.blog_post_category_codename as id
          FROM blog_post_categories AS node, blog_post_categories AS parent
          WHERE node.lft BETWEEN parent.lft AND parent.rgt
          GROUP BY node.blog_post_category_codename,node.blog_post_category_id,node.blog_post_category_name
          ORDER BY node.lft;
        ');
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
