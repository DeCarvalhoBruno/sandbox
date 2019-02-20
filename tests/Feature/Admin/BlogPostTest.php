<?php

namespace Tests\Feature;

use App\Models\Blog\BlogPost;
use App\Models\Blog\BlogLabelRecord;
use App\Models\Blog\BlogTag;
use App\Models\Entity;
use App\Support\Providers\Avatar;
use App\Support\Providers\File;
use App\Support\Providers\Image;
use App\Support\Providers\Media;
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
                'blog_post_user' => "john_doe",
                'published_at' => "201902051959",
            ]);
        $response->assertStatus(500);
        $this->assertEquals(BlogPost::query()->get()->count(), 0);
    }

    public function test_create_normal()
    {
        $u = $this->signIn()->createUser();

        $this->assertEquals(BlogPost::query()->get()->count(), 0);
        $response = $this->postJson(
            "/ajax/admin/blog/post/create",
            [
                'blog_status' => "BLOG_STATUS_DRAFT",
                'blog_post_title' => "dads",
                'blog_post_user' => $u->getAttribute('user_name'),
                'categories' => [],
                'published_at' => "201902051959",
                'tags' => []
            ]);
        $response->assertStatus(200);
        $this->assertEquals(BlogPost::query()->get()->count(), 1);
    }

//    public function test_create_without_title()
//    {
//        $this->withExceptionHandling();
//        $u = $this->signIn()->createUser();
//
//        $response = $this->postJson(
//            "/ajax/admin/blog/post/create",
//            [
//                'blog_status' => "BLOG_STATUS_DRAFT",
//                'blog_post_user' => $u->getAttribute('user_name'),
//                'categories' => [],
//                'published_at' => "201902051959",
//                'tags' => []
//            ]);
//        $response->assertStatus(422);
//    }

    public function test_edit()
    {
        $u = $this->signIn()->createUser();

        $this->postJson(
            "/ajax/admin/blog/post/create",
            [
                'blog_status' => "BLOG_STATUS_DRAFT",
                'blog_post_title' => "dads",
                'blog_post_user' => $u->getAttribute('user_name'),
                'categories' => [],
                'published_at' => "201902051959",
                'tags' => []
            ]);
        $this->assertEquals(BlogPostTag::query()->get()->count(), 0);
        $this->assertEquals(BlogLabelRecord::query()->get()->count(), 0);
        $string = 'modified post';
        $response = $this->postJson(
            "/ajax/admin/blog/post/edit/dads",
            [
                'blog_post_title' => $string,
                'tags' => ['dededed', 'deefegg'],
                'categories' => ['default']
            ]);
        $this->assertEquals(BlogPost::query()->first()->getAttribute('blog_post_title'), $string);
        $this->assertEquals(BlogPostTag::query()->get()->count(), 2);
        $this->assertEquals(BlogLabelRecord::query()->get()->count(), 3);
        $response->assertStatus(200);
    }

    public function test_delete()
    {
        $u = $this->signIn()->createUser();

        $this->postJson(
            "/ajax/admin/blog/post/create",
            [
                'blog_status' => "BLOG_STATUS_DRAFT",
                'blog_post_title' => "dads",
                'blog_post_user' => $u->getAttribute('user_name'),
                'published_at' => "201902051959",
            ]);
        $this->assertEquals(BlogPost::query()->get()->count(), 1);
        $response = $this->deleteJson('/ajax/admin/blog/post/dadsw');
        $response->assertStatus(200);
        $this->assertEquals(BlogPost::query()->get()->count(), 1);

        $this->assertEquals("0", $response->getContent());
        $response = $this->deleteJson('/ajax/admin/blog/post/dads');
        $response->assertStatus(200);
        $this->assertEquals("1", $response->getContent());
        $this->assertEquals(BlogPost::query()->get()->count(), 0);
    }

    public function test_upload_image()
    {
        $u = $this->signIn()->createUser();
        $postTitle = 'This is the title of my post';
        $this->postJson(
            "/ajax/admin/blog/post/create",
            [
                'blog_status' => "BLOG_STATUS_DRAFT",
                'blog_post_title' => $postTitle,
                'blog_post_user' => $u->getAttribute('user_name'),
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
        (new Media(new File(new Image(new Avatar()), new Text())))->image()
            ->getImagesFromSlug(
                slugify($postTitle),
                Entity::BLOG_POSTS,
                ['media_uuid']
            )->pluck('media_uuid')->all();
        $this->assertEquals($imageUuid, $responseArray[0]['uuid']);
    }

    public function test_upload_image_too_big()
    {
        $u = $this->signIn()->createUser();
        $postTitle = 'This is the title of my post';
        $this->postJson(
            "/ajax/admin/blog/post/create",
            [
                'blog_status' => "BLOG_STATUS_DRAFT",
                'blog_post_title' => $postTitle,
                'blog_post_user' => $u->getAttribute('user_name'),
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
        $u = $this->signIn()->createUser();
        $postTitle = 'This is the title of my post';
        $this->postJson(
            "/ajax/admin/blog/post/create",
            [
                'blog_status' => "BLOG_STATUS_DRAFT",
                'blog_post_title' => $postTitle,
                'blog_post_user' => $u->getAttribute('user_name'),
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

    public function test_delete_image()
    {
        $u = $this->signIn()->createUser();
        $postTitle = 'This is the title of my post';
        $this->postJson(
            "/ajax/admin/blog/post/create",
            [
                'blog_status' => "BLOG_STATUS_DRAFT",
                'blog_post_title' => $postTitle,
                'blog_post_user' => $u->getAttribute('user_name'),
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
        $this->assertEquals($response->json(),[]);
    }

}