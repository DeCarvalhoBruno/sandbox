<?php

namespace Tests\Feature\Admin;

use App\Models\Entity;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Naraki\Media\Facades\Media;
use Tests\TestCase;

class MediaTest extends TestCase
{
    use DatabaseMigrations, WithoutMiddleware;


    //@TODO: Make a thorough file upload series of tests once the feature is more solicited in the future.


    public function test_media_edit()
    {
        $this->withoutExceptionHandling();
        $u = $this->createUser();
        $this->signIn($u);
        $postTitle = 'This is the title of my post';
        $response = $this->postJson(
            "/ajax/admin/blog/post/create",
            [
                'blog_status' => "BLOG_STATUS_DRAFT",
                'blog_post_title' => $postTitle,
                'blog_post_person' => $u->getAttribute('person_slug'),
                'published_at' => "201902051959",
            ]);

        $imageTitle = 'mean_mug';
        $imageExtension = 'jpg';
        $imageFilename = sprintf('%s.%s', $imageTitle, $imageExtension);
        Storage::fake($imageFilename);

        $file = UploadedFile::fake()->image($imageFilename, 1224, 864)->size(16);

        $response = $this->postJson(
            "/ajax/admin/media/add",
            [
                'type' => "blog_posts",
                'target' => slugify($postTitle),
                'media' => 'image',
                'file' => $file
            ]
        );
        $responseArray = $response->json();
        $imageUuid = $responseArray[0]['uuid'];

        $alt = 'This appears in place of the image if the aforementioned does not load.';
        $caption = 'This caption appears underneath the image.';
        $desc = 'This description will be used for internal purposes to identify the image.';

        $response = $this->patchJson(
            '/ajax/admin/media/' . $imageUuid,
            [
                'media_alt' => $alt,
                'media_caption' => $caption,
                'media_description' => $desc
            ]
        );
        $response->assertStatus(204);
        $mediaDBFields = \Naraki\Media\Facades\Media::image()
            ->getImagesFromSlug(
                slugify($postTitle),
                Entity::BLOG_POSTS,
                ['media_alt', 'media_caption', 'media_description']
            )->toArray();

        $this->assertEquals([
            'media_alt' => $alt,
            'media_caption' => $caption,
            'media_description' => $desc
        ], $mediaDBFields[0]);
    }

    public function test_media_crop()
    {
        $u = $this->createUser();
        $this->signIn($u,'jwt');
        $imageTitle = 'mean_mug';
        $imageExtension = 'jpg';
        $imageFilename = sprintf('%s.%s', $imageTitle, $imageExtension);
        Storage::fake($imageFilename);

        $file = UploadedFile::fake()->image($imageFilename, 1224, 864)->size(1500);

        $response = $this->postJson(
            "/ajax/admin/media/add",
            [
                'type' => 'users',
                'target' => $u->getAttribute('username'),
                'media' => 'image_avatar',
                'file' => $file
            ]
        );
        $responseArray = $response->json();
        $imageDiskFilename = $responseArray['filename'];
        $this->postJson(
            '/ajax/admin/media/crop',
            [
                'height' => 864,
                'width' => 1224,
                'uuid' => substr($imageDiskFilename,0,-4),
                'x' => 250,
                'y' => 320
            ]
        );
        $avatar = Media::image()
            ->getImagesFromSlug(
                $u->getAttribute('username'),
                Entity::USERS,
                ['media_uuid']
            )->pluck('media_uuid')->first();

        $this->assertEquals(sprintf('%s.%s', $avatar, $imageExtension), $imageDiskFilename);
    }
}