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

            $table->string('media_title', 255)->nullable();
            $table->text('media_description')->nullable();
            $table->string('media_uuid',32);
            $table->boolean('media_in_use')->default(true);

            $table->index(['media_type_id', 'media_uuid'],'idx_media_type_uuid');
        });

        Schema::create('media_digital', function (Blueprint $table) {
            $table->increments('media_digital_id');

            $table->unsignedInteger('media_type_id');
            $table->string('media_filename', 255)->nullable();
            $table->string('media_extension', 10)->nullable();
            $table->string('media_thumbnail', 255)->nullable();
            $table->timestamps();

            $table->foreign('media_type_id')
                ->references('media_type_id')->on('media_types')
                ->onDelete('cascade');
        });

        Schema::create('media_records', function (Blueprint $table) {
            $table->increments('media_record_id');

            $table->unsignedInteger('media_type_id');
            $table->unsignedInteger('media_id');

            $table->foreign('media_type_id')
                ->references('media_type_id')->on('media_types')
                ->onDelete('cascade');
            $table->foreign('media_id')
                ->references('media_id')->on('media')
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

            $table->unsignedInteger('media_img_format_id')->default(\App\Models\Media\MediaImgFormat::ORIGINAL);

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

            $table->unsignedInteger('media_record_target_id')->comment('Either a media_record id or a media_group_record_id');
            $table->unsignedInteger('media_category_id')->default(\App\Models\Media\MediaCategory::MEDIA);

            $table->foreign('media_category_id')
                ->references('media_category_id')->on('media_categories')
                ->onDelete('cascade');
            $table->index(['media_category_record_id', 'media_record_target_id', 'media_category_id'],
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

            $table->index(['media_entity_id', 'entity_type_id', 'media_category_record_id'],
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
CREATE PROCEDURE sp_update_media__entity_in_use(IN in_media_entity_id BIGINT)
    MODIFIES SQL DATA
      BEGIN
            DECLARE var_entity INT DEFAULT 0;
    DECLARE var_category INT DEFAULT 0;
    DECLARE var_target INT DEFAULT 0;
    DECLARE var_media_is_used INT DEFAULT 0;
    
    SELECT
      entity_id,
      media_category_records.media_category_id,
      entity_types.entity_type_target_id,
      media_entity_in_use
    INTO var_entity, var_category, var_target, var_media_is_used
    FROM media_entities
      JOIN entity_types
        ON media_entities.entity_type_id = entity_types.entity_type_id
      JOIN media_category_records
        ON media_entities.media_category_record_id = media_category_records.media_category_record_id
    WHERE media_entity_id = in_media_entity_id;

    
    IF var_media_is_used = 1
    THEN
      UPDATE media_entities
      SET media_entity_in_use = 0
      WHERE media_entity_id IN
            (SELECT msei
             FROM (
                    SELECT media_entity_id AS msei
                    FROM media_entities
                      JOIN entity_types
                        ON media_entities.entity_type_id = entity_types.entity_type_id
                      JOIN media_category_records
                        ON media_entities.media_category_record_id =
                           media_category_records.media_category_record_id
                    WHERE media_entity_id != in_media_entity_id
                          AND entity_id = var_entity
                          AND media_category_records.media_category_id = var_category
                          AND entity_types.system_entity_type_target_id = var_target) AS mse);
    END IF;
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
