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
            $table->string('media_name', 75)->nullable();

            $table->index(array('lft', 'rgt', 'parent_id'));
        });

        Schema::create('media_types', function (Blueprint $table) {
            $table->increments('media_type_id');

            $table->unsignedInteger('media_id');

            $table->string('media_title')->nullable();
            $table->text('media_description')->nullable();
            $table->string('media_uuid', 32);
            $table->boolean('media_in_use')->default(true);

            $table->foreign('media_id')
                ->references('media_id')->on('media')
                ->onDelete('cascade');
            $table->unique(['media_type_id', 'media_uuid'], 'idx_media_type_uuid');
        });

        Schema::create('media_digital', function (Blueprint $table) {
            $table->increments('media_digital_id');

            $table->unsignedInteger('media_type_id');
            $table->string('media_filename')->nullable();
            $table->string('media_extension', 10)->nullable();
            $table->string('media_alt')->nullable();
            $table->text('media_caption')->nullable();
            $table->timestamps();

            $table->foreign('media_type_id')
                ->references('media_type_id')->on('media_types')
                ->onDelete('cascade');
        });

        Schema::create('media_img', function (Blueprint $table) {
            $table->increments('media_img_id');

            $table->unsignedInteger('media_digital_id');
            $table->string('media_img_alt')->nullable();
            $table->text('media_img_attribution')->nullable();

            $table->foreign('media_digital_id')
                ->references('media_digital_id')->on('media_digital')
                ->onDelete('cascade');
        });

        Schema::create('media_records', function (Blueprint $table) {
            $table->increments('media_record_id');

            $table->unsignedInteger('media_type_id');

            $table->foreign('media_type_id')
                ->references('media_type_id')->on('media_types')
                ->onDelete('cascade');
        });

        Schema::create('media_img_formats', function (Blueprint $table) {
            $table->increments('media_img_format_id');

            $table->string('media_img_format_name', 50);
            $table->unsignedInteger('media_img_format_width')->default(0);
            $table->unsignedInteger('media_img_format_height')->default(0);
            $table->string('media_img_format_acronym', 20)->nullable();
        });

        Schema::create('media_img_format_types', function (Blueprint $table) {
            $table->increments('media_img_format_type_id');
            $table->unsignedInteger('media_digital_id');

            $table->unsignedInteger('media_img_format_id')
                ->default(\App\Models\Media\MediaImgFormat::ORIGINAL);

            $table->foreign('media_digital_id', 'fk_img_format_types')
                ->references('media_digital_id')->on('media_digital')
                ->onDelete('cascade');
            $table->foreign('media_img_format_id', 'fk_img_formats')
                ->references('media_img_format_id')->on('media_img_formats')
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
            $table->string('media_group_type_slug', 100)->nullable();
            $table->timestamps();

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

        Schema::create('media_category_records', function (Blueprint $table) {
            $table->increments('media_category_record_id');

            $table->unsignedInteger('media_record_target_id')->comment(
                'Either a media_record id or a media_group_record_id');
            $table->unsignedInteger('media_category_id')->default(
                \App\Models\Media\MediaCategory::MEDIA);

            $table->foreign('media_category_id')
                ->references('media_category_id')->on('media_categories')
                ->onDelete('cascade');
            $table->unique(['media_category_record_id', 'media_record_target_id', 'media_category_id'],
                'idx_media_category_record');
        });

        Schema::create('media_entities', function (Blueprint $table) {
            $table->increments('media_entity_id');

            $table->unsignedInteger('entity_type_id');
            $table->unsignedInteger('media_category_record_id');

            $table->foreign('entity_type_id')
                ->references('entity_type_id')->on('entity_types')
                ->onDelete('cascade');

            $table->foreign('media_category_record_id')
                ->references('media_category_record_id')->on('media_category_records')
                ->onDelete('cascade');

            $table->unique(['media_entity_id', 'entity_type_id', 'media_category_record_id'],
                'idx_media_entity_category');
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
        $this->imageFormats();
        if (App::environment() !== 'testing') {
            $this->mediaInUseProcedure();
        }
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

    public function imageFormats()
    {
        $imageFormats = [
            [
                'media_img_format_name' => 'ORIGINAL',
                'media_img_format_width' => 0,
                'media_img_format_height' => 0
            ]
        ];
        \App\Models\Media\MediaImgFormat::insert($imageFormats);

    }

    private function mediaInUseProcedure()
    {
        $sql = <<<SQL
CREATE PROCEDURE sp_update_media_type_in_use(IN in_media_uuid VARCHAR(32))
MODIFIES SQL DATA
  BEGIN
    UPDATE media_types
    SET media_in_use = 0
    WHERE media_type_id in (
      select mti
      from (
             select media_types.media_type_id as mti
             from media_types
               join media_records on media_types.media_type_id = media_records.media_type_id
               join media_category_records
                 on media_category_records.media_record_target_id = media_records.media_record_id
               join media_entities
                 on media_entities.media_category_record_id = media_category_records.media_category_record_id
             where media_uuid != in_media_uuid) as mt);
    UPDATE media_types
      SET media_in_use=1
    where media_uuid=in_media_uuid;
  END;
SQL;
        \DB::connection()->getPdo()->exec($sql);
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
