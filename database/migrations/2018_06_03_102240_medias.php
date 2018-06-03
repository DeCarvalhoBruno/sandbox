<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Media\Media;
use App\Models\Media\MediaGroup;

class Medias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->increments('media_id');

            $table->unsignedInteger('parent_id')->nullable();
            $table->unsignedInteger('lft')->default(0);
            $table->unsignedInteger('rgt')->default(0);
            $table->string('media_name',75)->nullable();

            $table->index(array('lft', 'rgt', 'parent_id'));
        });

        Schema::create('media_types', function (Blueprint $table) {
            $table->increments('media_type_id');

            $table->string('media_type_title', 255)->nullable();
            $table->text('media_type_description')->nullable();
            $table->string('media_type_thumbnail', 255)->nullable();
        });

        Schema::create('media_type_txt', function (Blueprint $table) {
            $table->increments('media_type_txt_id');
            $table->unsignedInteger('media_type_id');

            $table->string('media_type_txt_filename', 255)->nullable();
            $table->string('media_type_txt_original_filename', 255)->nullable();

            $table->foreign('media_type_id')
                ->references('media_type_id')->on('media_types')
                ->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('media_type_img', function (Blueprint $table) {
            $table->increments('media_type_img_id');

            $table->unsignedInteger('media_type_id');
            $table->foreign('media_type_id')
                ->references('media_type_id')->on('media_types')
                ->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('media_type_img_formats', function (Blueprint $table) {
            $table->increments('media_type_img_format_id');

            $table->string('media_type_img_format_name', 50)->nullable();
            $table->unsignedInteger('media_type_img_format_width')->nullable();
            $table->unsignedInteger('media_type_img_format_height')->nullable();
            $table->string('media_type_img_format_acronym', 20)->nullable();
        });

        Schema::create('media_type_img_format_types', function (Blueprint $table) {
            $table->increments('media_type_img_format_type_id');
            $table->unsignedInteger('media_type_img_id');
            $table->unsignedInteger('media_type_img_format_id');

            $table->string('media_type_img_filename', 255)->nullable();
            $table->string('media_type_img_original_filename', 255)->nullable();
            $table->timestamps();

            $table->foreign('media_type_img_id', 'fk_img_format_types')
                ->references('media_type_img_id')->on('media_type_img')
                ->onDelete('cascade');
            $table->foreign('media_type_img_format_id', 'fk_img_formats')
                ->references('media_type_img_format_id')->on('media_type_img_formats')
                ->onDelete('cascade');

        });

        Schema::create('media_records', function (Blueprint $table) {
            $table->increments('media_record_id');

            $table->unsignedInteger('media_type_id');
            $table->unsignedInteger('media_id');

            $table->foreign('media_id')
                ->references('media_id')->on('media')
                ->onDelete('cascade');

            $table->foreign('media_type_id')
                ->references('media_type_id')->on('media_types')
                ->onDelete('cascade');
        });

        Schema::create('media_groups', function (Blueprint $table) {
            $table->increments('media_group_id');

            $table->unsignedInteger('parent_id')->nullable();
            $table->unsignedInteger('lft')->default(0);
            $table->unsignedInteger('rgt')->default(0);
            $table->string('media_group_name', 75)->nullable();

            $table->index(array('lft', 'rgt', 'parent_id'));
        });

        Schema::create('media_group_types', function (Blueprint $table) {
            $table->increments('media_group_type_id');

            $table->unsignedInteger('media_group_id');
            $table->string('media_group_type_title', 100)->nullable();

            $table->foreign('media_group_id')
                ->references('media_group_id')->on('media_groups')
                ->onDelete('cascade');
        });

        Schema::create('media_group_records', function (Blueprint $table) {
            $table->increments('media_group_record_id');

            $table->unsignedInteger('media_group_type_id');
            $table->unsignedInteger('media_record_id');

            $table->foreign('media_group_type_id')
                ->references('media_group_type_id')->on('media_group_types')
                ->onDelete('cascade');

            $table->foreign('media_record_id')
                ->references('media_record_id')->on('media_records')
                ->onDelete('cascade');
        });

        Schema::create('media_categories', function (Blueprint $table) {
            $table->increments('media_category_id');

            $table->string('media_category_name', 75)->nullable();
        });

        Schema::create('media_entities', function (Blueprint $table) {
            $table->increments('media_entity_id');

            $table->unsignedInteger('entity_type_id');
            $table->unsignedInteger('media_record_id');
            $table->unsignedInteger('media_category_id')->default(\App\Models\Media\MediaCategory::MEDIA);
            $table->boolean('media_entity_in_use')->default(true);

            $table->foreign('entity_type_id')
                ->references('entity_type_id')->on('entity_types')
                ->onDelete('cascade');

            $table->foreign('media_category_id')
                ->references('media_category_id')->on('media_categories')
                ->onDelete('cascade');

            $table->index(['media_entity_id', 'entity_type_id', 'media_category_id'],
                'idx_entity_category_media');
        });

        $categories = [
            [
                'media_category_name' => 'MEDIA'
            ],
            [
                'media_category_name' => 'MEDIA_GROUP'
            ]
        ];
        \App\Models\Media\MediaCategory::insert($categories);

        $this->addMediaGroups();
        $this->addMedia();
    }

    private function addMedia()
    {
        $nameColumn = 'media_name';
        $mediaIdColumn = 'media_id';

        $digital = [
            $nameColumn => 'DIGITAL',
            $mediaIdColumn => Media::DIGITAL,
            'children' => [
                [
                    $nameColumn => 'VIDEO',
                    $mediaIdColumn => Media::VIDEO,
                ],
                [
                    $nameColumn => 'AUDIO',
                    $mediaIdColumn => Media::AUDIO,
                ],
                [
                    $nameColumn => 'TEXT',
                    $mediaIdColumn => Media::TEXT,
                ],
                [
                    $nameColumn => 'IMAGE',
                    $mediaIdColumn => Media::IMAGE,
                    'children' => [
                        [
                            $nameColumn => 'IMAGE_AVATAR',
                            $mediaIdColumn => Media::IMAGE_AVATAR,
                        ]
                    ],
                ]
            ]
        ];
        Media::create($digital);
    }

    private function addMediaGroups()
    {
        $nameColumn = 'media_group_name';
        $files = [
            $nameColumn => 'TEXT',
            'id' => MediaGroup::TEXT,
            'children' => [
                [
                    $nameColumn => 'TEXT_LIBRARY',
                    'id' => MediaGroup::TEXT_LIBRARY,
                ]
            ],

        ];
        $images = [
            $nameColumn => 'IMAGE',
            'id' => MediaGroup::IMAGE,
            'children' => [
                [
                    $nameColumn => 'IMAGE_GALLERY',
                    'id' => MediaGroup::IMAGE_GALLERY,
                ],
                [
                    $nameColumn => 'IMAGE_LIBRARY',
                    'id' => MediaGroup::IMAGE_LIBRARY,
                ]
            ],

        ];
        MediaGroup::create($files);
        MediaGroup::create($images);
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
