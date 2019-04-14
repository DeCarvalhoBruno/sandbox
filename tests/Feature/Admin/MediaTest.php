<?php

namespace Tests\Feature\Admin;

use Naraki\Core\Models\Entity;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Naraki\Elasticsearch\Facades\ElasticSearchIndex;
use Naraki\Media\Facades\Media;
use Tests\TestCase;

class MediaTest extends TestCase
{
    use DatabaseMigrations, WithoutMiddleware;


    public function test_media_show()
    {
        $u = $this->createUser();

        $this->signIn($u,'jwt');
        $response = $this->getJson(
            "/ajax/admin/media"
        );
        $response->assertStatus(200);
    }


    public function test_media_edit()
    {
        $this->withoutExceptionHandling();
        $u = $this->createUser();
        $this->signIn($u);
        $postTitle = 'This is the title of my post';
        ElasticSearchIndex::shouldReceive('index')->times(1);
        $this->postJson(
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

        $file = UploadedFile::fake()->image($imageFilename, 600, 300)->size(10);

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

        $file = UploadedFile::fake()->image($imageFilename, 600, 300)->size(10);

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
            '/ajax/admin/media/crop/avatar',
            [
                'height' => 600,
                'width' => 300,
                'uuid' => $imageDiskFilename,
                'x' => 50,
                'y' => 10
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