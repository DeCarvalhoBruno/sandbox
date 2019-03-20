<?php

namespace Tests\Feature\Admin;

use Naraki\Blog\Models\BlogPost;
use Naraki\Blog\Models\BlogLabelRecord;
use Naraki\Blog\Models\BlogStatus;
use Naraki\Blog\Models\BlogTag;
use App\Models\Entity;
use App\Support\Providers\Avatar;
use App\Support\Providers\File;
use App\Support\Providers\Image;
use Naraki\Media\Providers\Media;
use App\Support\Providers\Text;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlogPostTest extends TestCase
{
    use DatabaseMigrations, WithoutMiddleware;

    public function test_create_non_existing_user()
    {
        $this->withExceptionHandling();
        $response = $this->postJson(
            "/ajax/admin/blog/post/create",
            [
                'blog_status' => "BLOG_STATUS_DRAFT",
                'blog_post_title' => "dads",
                'blog_post_person' => "john_doe",
                'published_at' => "201902051959",
            ]);

        $response->assertStatus(500);
        $this->assertEquals('Person for blog post creation not found.',$response->content());
        $this->assertEquals(BlogPost::query()->get()->count(), 0);
    }

    public function test_create_normal()
    {
        $u = $this->createUser();
        $this->signIn($u);

        $this->assertEquals(BlogPost::query()->get()->count(), 0);
        $response = $this->postJson(
            "/ajax/admin/blog/post/create",
            [
                'blog_status' => "BLOG_STATUS_DRAFT",
                'blog_post_title' => "dads",
                'blog_post_person' => $u->getAttribute('person_slug'),
                'categories' => [],
                'published_at' => "201902051959",
                'tags' => []
            ]);

        $response->assertStatus(200);
        $this->assertEquals(BlogPost::query()->get()->count(), 1);
    }

    public function test_create_without_title()
    {
        $this->withExceptionHandling();
        $u = $this->createUser();
        $this->signIn($u);

        $response = $this->postJson(
            "/ajax/admin/blog/post/create",
            [
                'blog_status' => "BLOG_STATUS_DRAFT",
                'blog_post_person' => $u->getAttribute('person_slug'),
                'categories' => [],
                'published_at' => "201902051959",
                'tags' => []
            ]);
        $response->assertStatus(422);
    }

    public function test_edit()
    {
        $u = $this->createUser();
        $this->signIn($u);
        $this->postJson(
            "/ajax/admin/blog/post/create",
            [
                'blog_status' => "BLOG_STATUS_DRAFT",
                'blog_post_title' => "dads",
                'blog_post_person' => $u->getAttribute('person_slug'),
                'categories' => [],
                'published_at' => "201902051959",
                'tags' => []
            ]);
        $this->assertEquals(0, BlogTag::query()->get()->count());
        $this->assertEquals(1, BlogPost::query()->get()->count());
        $this->assertEquals(0, BlogLabelRecord::query()->get()->count());
        $string = 'modified post';
        $response = $this->postJson(
            "/ajax/admin/blog/post/edit/dads",
            [
                'blog_post_title' => $string,
                'tags' => ['dededed', 'deefegg'],
                'categories' => ['default']
            ]);
        $this->assertEquals(BlogPost::query()->first()->getAttribute('blog_post_title'), $string);
        $this->assertEquals(BlogTag::query()->get()->count(), 2);
        $this->assertEquals(BlogLabelRecord::query()->get()->count(), 3);
        $response->assertStatus(200);
    }

    public function test_delete_post()
    {
        $u = $this->createUser();
        $this->signIn($u);

        $this->postJson(
            "/ajax/admin/blog/post/create",
            [
                'blog_status' => "BLOG_STATUS_DRAFT",
                'blog_post_title' => "dads",
                'blog_post_person' => $u->getAttribute('person_slug'),
                'published_at' => "201902051959",
            ]);
        $this->assertEquals(BlogPost::query()->get()->count(), 1);
        $response = $this->deleteJson('/ajax/admin/blog/post/dadsw');
        $response->assertStatus(204);
        $this->assertEquals(BlogPost::query()->get()->count(), 1);

        $response = $this->deleteJson('/ajax/admin/blog/post/dads');
        $response->assertStatus(204);
        $this->assertEquals(BlogPost::query()->get()->count(), 0);
    }

    public function test_upload_image()
    {
        $u = $this->createUser();
        $this->signIn($u);

        $postTitle = 'This is the title of my post';
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
        $this->assertArrayHasKey('uuid', $responseArray[0]);
        $imageUuid = $responseArray[0]['uuid'];
        \Naraki\Media\Facades\Media::image()
            ->getImagesFromSlug(
                slugify($postTitle),
                Entity::BLOG_POSTS,
                ['media_uuid']
            )->pluck('media_uuid')->all();
        $this->assertEquals($imageUuid, $responseArray[0]['uuid']);
    }

    public function test_upload_image_too_big()
    {
        $u = $this->createUser();
        $this->signIn($u);
        $postTitle = 'This is the title of my post';
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

        $file = UploadedFile::fake()->image($imageFilename, 1224, 864)->size(2000);

        $response = $this->postJson(
            "/ajax/admin/media/add",
            [
                'type' => "blog_posts",
                'target' => slugify($postTitle),
                'media' => 'image',
                'file' => $file
            ]
        );
        $response->assertStatus(500);
    }

    public function test_upload_image_without_file()
    {
        $u = $this->createUser();
        $this->signIn($u);
        $postTitle = 'This is the title of my post';
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

        $file = UploadedFile::fake()->image($imageFilename, 1224, 864)->size(16);

        $response = $this->postJson(
            "/ajax/admin/media/add",
            [
                'type' => "blog_posts",
                'target' => slugify($postTitle),
                'media' => 'image',
            ]
        );
        $response->assertStatus(500);
    }

    public function delete_image()
    {
        $u = $this->createUser();
        $this->signIn($u);
        $postTitle = 'This is the title of my post';
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
        $response = $this->deleteJson(
            sprintf(
                '/ajax/admin/blog/post/edit/%s/image/%s',
                slugify($postTitle),
                $imageUuid
            )
        );
        $response->assertStatus(200);
        $this->assertEquals($response->json(), []);
    }

}