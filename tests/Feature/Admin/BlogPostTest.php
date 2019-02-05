<?php

namespace Tests\Feature;

use App\Models\Blog\BlogPost;
use App\Models\Blog\BlogPostCategory;
use App\Models\Blog\BlogPostLabelRecord;
use App\Models\Blog\BlogPostTag;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
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
                'blog_post_status' => "BLOG_POST_STATUS_DRAFT",
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
                'blog_post_status' => "BLOG_POST_STATUS_DRAFT",
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
//                'blog_post_status' => "BLOG_POST_STATUS_DRAFT",
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
                'blog_post_status' => "BLOG_POST_STATUS_DRAFT",
                'blog_post_title' => "dads",
                'blog_post_user' => $u->getAttribute('user_name'),
                'categories' => [],
                'published_at' => "201902051959",
                'tags' => []
            ]);
        $this->assertEquals(BlogPostTag::query()->get()->count(), 0);
        $this->assertEquals(BlogPostLabelRecord::query()->get()->count(), 0);
        $string = 'modified post';
        $response = $this->postJson(
            "/ajax/admin/blog/post/edit/dads",
            [
                'blog_post_title' => $string,
                'tags' => ['dededed', 'deefegg'],
                'categories' => ['default']
            ]);
        $this->assertEquals(BlogPost::query()->first()->getAttribute('blog_post_title'),$string );
        $this->assertEquals(BlogPostTag::query()->get()->count(), 2);
        $this->assertEquals(BlogPostLabelRecord::query()->get()->count(), 3);
        $response->assertStatus(200);
    }

    public function test_delete()
    {
        $u = $this->signIn()->createUser();

        $this->postJson(
            "/ajax/admin/blog/post/create",
            [
                'blog_post_status' => "BLOG_POST_STATUS_DRAFT",
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

}