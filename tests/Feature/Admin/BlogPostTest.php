<?php

namespace Tests\Feature\Admin;

use Naraki\Core\Models\Entity;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Naraki\Blog\Models\BlogLabelRecord;
use Naraki\Blog\Models\BlogPost;
use Naraki\Blog\Models\BlogTag;
use Naraki\Elasticsearch\Facades\ElasticSearchIndex;
use Tests\TestCase;

class BlogPostTest extends TestCase
{
    use DatabaseMigrations, WithoutMiddleware;

    public function test_blog_post_no_auth(){
        $this->withMiddleware();
        $response = $this->postJson(
            "/ajax/admin/blog/post/create",
            [
                'blog_post_title' => "dads",
            ]);
        $response->assertStatus(401);
    }

    public function test_blog_post_create_non_existing_user()
    {
        $this->withExceptionHandling();
        ElasticSearchIndex::shouldReceive('index')->times(0);
        $response = $this->postJson(
            "/ajax/admin/blog/post/create",
            [
                'blog_status' => "BLOG_STATUS_DRAFT",
                'blog_post_title' => "dads",
                'blog_post_person' => "john_doe",
                'published_at' => "201902051959",
            ]);
        $response->assertStatus(500);
        $this->assertEquals('Person for blog post creation not found.', $response->content());
        $this->assertEquals(0, $this->cnt(BlogPost::class));
    }

    public function test_blog_post_create_normal()
    {
        $u = $this->createUser();
        $this->assertEquals(0, $this->cnt(BlogPost::class));
        ElasticSearchIndex::shouldReceive('index')->times(1);
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
        $this->assertEquals(1, $this->cnt(BlogPost::class));
    }

    public function test_blog_post_create_without_title()
    {
        $this->withExceptionHandling();
        $u = $this->createUser();
        ElasticSearchIndex::shouldReceive('index')->times(0);
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

    public function test_blog_post_edit()
    {
        $u = $this->createUser();
        ElasticSearchIndex::shouldReceive('index')->times(1);
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
        $this->assertEquals(0, $this->cnt(BlogTag::class));
        $this->assertEquals(1, $this->cnt(BlogPost::class));
        $this->assertEquals(0, $this->cnt(BlogLabelRecord::class));
        $string = 'modified post';
        ElasticSearchIndex::shouldReceive('update')->times(1);
        ElasticSearchIndex::shouldReceive('index')->times(2);
        $response = $this->postJson(
            "/ajax/admin/blog/post/edit/dads",
            [
                'blog_post_title' => $string,
                'tags' => ['dededed', 'deefegg'],
                'categories' => ['default']
            ]);
        $this->assertEquals($string, BlogPost::query()->first()->getAttribute('blog_post_title'));
        $this->assertEquals(2, $this->cnt(BlogTag::class));
        $this->assertEquals(3, $this->cnt(BlogLabelRecord::class));
        $response->assertStatus(200);
    }

    public function test_blog_post_modify_tags()
    {
        $u = $this->createUser();
        ElasticSearchIndex::shouldReceive('index')->times(6);
        $this->postJson(
            "/ajax/admin/blog/post/create",
            [
                'blog_status' => "BLOG_STATUS_DRAFT",
                'blog_post_title' => "dads",
                'blog_post_person' => $u->getAttribute('person_slug'),
                'categories' => [],
                'published_at' => "201902051959",
                'tags' => ['tag1', 'tag2', 'tag3']
            ]);
        $this->assertEquals(3, $this->cnt(BlogTag::class));
        $this->assertEquals(3, $this->cnt(BlogLabelRecord::class));

        $string = 'modified post';
        ElasticSearchIndex::shouldReceive('destroy')->twice();
        ElasticSearchIndex::shouldReceive('update')->once();
        $this->postJson(
            "/ajax/admin/blog/post/edit/dads",
            [
                'blog_post_title' => $string,
                'tags' => ['tag1', 'tag4'],
                'categories' => ['default']
            ]);
        $this->assertEquals(2, $this->cnt(BlogTag::class));
        $this->assertEquals(3, $this->cnt(BlogLabelRecord::class));
    }

    public function test_blog_post_delete_post()
    {
        $u = $this->createUser();
        ElasticSearchIndex::shouldReceive('index')->times(1);
        $this->postJson(
            "/ajax/admin/blog/post/create",
            [
                'blog_status' => "BLOG_STATUS_DRAFT",
                'blog_post_title' => "dads",
                'blog_post_person' => $u->getAttribute('person_slug'),
                'published_at' => "201902051959",
            ]);
        $this->assertEquals(1, $this->cnt(BlogPost::class));
        ElasticSearchIndex::shouldReceive('destroy')->times(1);
        $response = $this->deleteJson('/ajax/admin/blog/post/dadsw');
        $response->assertStatus(204);
        $this->assertEquals(1, $this->cnt(BlogPost::class));

        $response = $this->deleteJson('/ajax/admin/blog/post/dads');
        $response->assertStatus(204);
        $this->assertEquals(0, $this->cnt(BlogPost::class));
    }

    public function test_blog_post_upload_image()
    {
        $u = $this->createUser();

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

    public function test_blog_post_upload_image_too_big()
    {
        $u = $this->createUser();

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

    public function test_blog_post_upload_image_without_file()
    {
        $u = $this->createUser();

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

    public function test_delete_blog_post_image()
    {
        $u = $this->createUser();

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