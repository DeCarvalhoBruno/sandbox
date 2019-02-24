<?php

use Illuminate\Database\Seeder;
use App\Support\Media\ImageProcessor;
use App\Models\Media\MediaImgFormat;

class ImageSeeder extends Seeder
{
    private $origDir = __DIR__ . '/../files/data/img';

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        $images = $this->parseImages();
        $posts = [];
//        dd($images);

        $postSlugIds = \App\Models\Blog\BlogPost::query()->select([
            'blog_post_slug',
            'blog_post_id',
            'entity_types.entity_type_id'
        ])
            ->entityType()
            ->get();
        foreach ($postSlugIds as $slug) {
            $posts[$slug['blog_post_slug']] = (object)[
                'entity' => $slug['entity_type_id'],
                'post' => $slug['blog_post_id']
            ];
        }

//        $f = array_chunk($posts, 100, true);
//        dd(($f[0]));

        foreach ($images as $image => $extension) {
            if (isset($posts[$image])) {
                $uuid = makeHexUuid();
                \DB::beginTransaction();
                $this->saveImage(
                    sprintf('%s/%s.%s', $this->origDir, $image, $extension),
                    $extension,
                    $uuid
                );
                $this->saveDb($uuid, $extension, $posts[$image]->entity,
                    [MediaImgFormat::FEATURED]);
                \DB::commit();
            }
        }
    }

    private function saveDb($uuid, $fileExtension, $entityTypeID, $formats)
    {
        $filename = sprintf('%s.%s', $uuid, $fileExtension);
        $mediaType = \App\Models\Media\MediaType::create([
            'media_title' => $filename,
            'media_uuid' => $uuid,
            'media_id' => \App\Models\Media\Media::IMAGE,
            'media_in_use' => 1
        ]);

        $mediaDigital = \App\Models\Media\MediaDigital::create([
            'media_type_id' => $mediaType->getKey(),
            'media_extension' => $fileExtension,
            'media_filename' => $filename,
        ]);

        $mediaRecord = \App\Models\Media\MediaRecord::create([
            'media_type_id' => $mediaType->getKey(),
        ]);

        $mediaCategoryRecord = \App\Models\Media\MediaCategoryRecord::create([
            'media_record_target_id' => $mediaRecord->getKey(),
        ]);

        \App\Models\Media\MediaEntity::create([
            'entity_type_id' => $entityTypeID,
            'media_category_record_id' => $mediaCategoryRecord->getKey(),
        ]);

        \App\Models\Media\MediaImg::create([
            'media_digital_id' => $mediaDigital->getKey()
        ]);

        if (!is_null($formats)) {
            foreach ($formats as $format) {
                \App\Models\Media\MediaImgFormatType::create([
                    'media_digital_id' => $mediaDigital->getKey(),
                    'media_img_format_id' => $format
                ]);
            }
        }
    }

    private function saveImage($path, $fileExtension, $uuid)
    {

        ImageProcessor::saveImg(
            ImageProcessor::makeCroppedImage($path),
            media_entity_root_path(
                \App\Models\Entity::BLOG_POSTS,
                \App\Models\Media\Media::IMAGE,
                ImageProcessor::makeFormatFilenameFromImageFilename(
                    sprintf('%s.%s', $uuid, $fileExtension)
                )
            )
        );

        ImageProcessor::saveImg(
            ImageProcessor::makeCroppedImage(
                $path,
                \App\Models\Media\MediaImgFormat::FEATURED
            ),
            media_entity_root_path(
                \App\Models\Entity::BLOG_POSTS,
                \App\Models\Media\Media::IMAGE,
                ImageProcessor::makeFormatFilename($uuid, $fileExtension, \App\Models\Media\MediaImgFormat::FEATURED)
            )
        );

//        ImageProcessor::saveImg(
//            ImageProcessor::makeCroppedImage(
//                $path,
//                \App\Models\Media\MediaImgFormat::HD
//            ),
//            media_entity_root_path(
//                \App\Models\Entity::BLOG_POSTS,
//                \App\Models\Media\Media::IMAGE,
//                ImageProcessor::makeFormatFilename($uuid, $fileExtension, \App\Models\Media\MediaImgFormat::HD)
//            )
//        );

//        ImageProcessor::copyImg(
//            $path,
//            media_entity_root_path(
//                \App\Models\Entity::BLOG_POSTS,
//                \App\Models\Media\Media::IMAGE,
//                ImageProcessor::makeFormatFilename($uuid, $fileExtension, \App\Models\Media\MediaImgFormat::ORIGINAL)
//            )
//        );

    }

    private function seedChunk($data, $model, $nbChunks = 25)
    {
        $chunks = array_chunk($data, $nbChunks);
        foreach ($chunks as $chunk) {
            forward_static_call(sprintf('%s::insert', $model), $chunk);
        }
    }

    private function parseImages()
    {


        $dir = opendir($this->origDir);
        if (!$dir) {
            die(sprintf("%s could not be read.", $this->origDir));
        }
        $productId = 1;
        //We wanna insert products in the database before the rest because of the integrity checks.
        $images = [];
        while (($file = readdir($dir)) !== false) {
            $fullPath = $this->origDir . '/' . $file;
            if (is_file($fullPath)) {
                $pos = strrpos($file, '.');
                $images[substr($file, 0, $pos)] = substr($file, $pos + 1);
            }
        }
        closedir($dir);
        return $images;
    }


}
