<?php namespace App\Support\Media;

use App\Models\Media\MediaImgFormat;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\Image as InterventionImage;
use Intervention\Image\ImageManagerStatic;

class ImageProcessor extends InterventionImage
{
    public static $thumbnailHeight = 128;
    public static $thumbnailWidth = 128;
    public static $imageDriver = 'imagick';
    public static $quality = 80;

    public static function makeImg($path)
    {
        ImageManagerStatic::configure(array('driver' => static::$imageDriver));
        try {
            $image = ImageManagerStatic::make($path);
        } catch (NotReadableException $e) {
            \File::delete($path);
            throw new NotReadableException($e->getMessage());
        }

        return $image;
    }

    /**
     *
     * @param $path
     * @param int $formatId
     * @return \Intervention\Image\Image|string The resulting file's name or the image object
     */
    public static function makeCroppedImage($path, $formatId)
    {
        return static::resizeToFormat(static::makeImg($path), $formatId);
    }

    /**
     * @param \Intervention\Image\Image $image
     * @param int $formatId
     *
     * @return \Intervention\Image\Image|string
     */
    private static function resizeToFormat(
        $image,
        $formatId
    ) {
        $format = MediaImgFormat::getFormatDimensions($formatId);

        if ($image->getHeight() > $format->height || $image->getWidth() > $format->width) {
            $image->fit($format->width, $format->height, function ($constraint) {
                /**
                 * @var \Intervention\Image\Constraint $constraint
                 */
                //Maintain aspect ratio
                $constraint->aspectRatio();
                //Prevent upsizing
                $constraint->upsize();
            });
        }
        return $image;
    }

    /**
     * @param \Intervention\Image\Image $image
     * @param int|null $fullPath
     * @return \Intervention\Image\Image|void
     */
    public static function saveImg($image, $fullPath)
    {
        $image->save($fullPath, static::$quality);
    }

    /**
     * @param string $filename
     * @param int $formatID
     *
     * @return string
     */
    public static function makeFormatFilenameFromImageFilename($filename, $formatID = MediaImgFormat::THUMBNAIL)
    {
        $extensionPosition = strrpos($filename, ".");

        return sprintf('%s_%s.%s', slugify(substr($filename, 0, $extensionPosition)),
            MediaImgFormat::getFormatAcronyms($formatID),
            substr($filename, $extensionPosition + 1));
    }

}