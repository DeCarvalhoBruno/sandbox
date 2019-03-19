<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Naraki\Blog\Job\UpdateElasticsearch;
use Tests\TestCase;

class UpdateElasticSearchTest extends TestCase
{
    use DatabaseMigrations, WithoutMiddleware;

    /**
     * A basic test example.
     *
     * @return void
     * @throws \Exception
     */
    public function test_basic()
    {
        $this->withoutExceptionHandling();
        $u = $this->createUser();
        $this->postJson(
            "/ajax/admin/blog/categories",
            ['label' => 'cat1', 'parent' => '']);
        $this->postJson(
            "/ajax/admin/blog/categories",
            ['label' => 'cat2', 'parent' => '']);
        $this->postJson(
            "/ajax/admin/blog/categories",
            ['label' => 'cat3', 'parent' => '']);
        $this->postJson(
            "/ajax/admin/blog/categories",
            ['label' => 'cat11', 'parent' => 'cat1']);
        $this->postJson(
            "/ajax/admin/blog/categories",
            ['label' => 'cat12', 'parent' => 'cat1']);
        $this->postJson(
            "/ajax/admin/blog/categories",
            ['label' => 'cat111', 'parent' => slugify('cat11')]);
        $this->postJson(
            "/ajax/admin/blog/post/create",
            [
                'blog_status' => "BLOG_STATUS_DRAFT",
                'blog_post_title' => "dads",
                'blog_post_person' => $u->getAttribute('person_slug'),
                'categories' => ['cat111'],
                'published_at' => "201902051959",
                'tags' => []
            ]);
        $job = new UpdateElasticsearch(
            (object)[
                "entity_type_id" => 1,
                "blog_post_id" => 1,
            ],
            (object)[
                "blog_post_title" => "EasyJet : un scan des bagages cabines en VR dans l'application",
                "blog_post_content" => "content",
                "blog_post_excerpt" => "EasyJet : un scan des bagages cabines en VR dans l'application",
                "published_at" => '2019-01-23 07:34:00',
                "blog_status_id" => 3,
                "person_id" => 2,
            ],
            [
                'added' => ['tag1', 'tag2'],
                'removed' => 6069
            ],
            true
        );
        $job->handle();
        $this->assertTrue(true);
    }
}
