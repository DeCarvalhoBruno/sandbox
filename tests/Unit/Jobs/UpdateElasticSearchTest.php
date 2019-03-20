<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Bus;
use Naraki\Blog\Job\UpdateElasticsearch;
use Naraki\Blog\Models\BlogPost;
use Naraki\Elasticsearch\Facades\ElasticSearchIndex;
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
        $u = $this->createUser();
        $this->postJson(
            '/ajax/admin/blog/categories',
            ['label' => 'cat1', 'parent' => '']);
        $this->postJson(
            '/ajax/admin/blog/categories',
            ['label' => 'cat11', 'parent' => 'cat1']);
        $this->postJson(
            '/ajax/admin/blog/categories',
            ['label' => 'cat111', 'parent' => slugify('cat11')]);
        ElasticSearchIndex::shouldReceive('create')->once();
        $blogPostData = [
            'blog_status' => 'BLOG_STATUS_DRAFT',
            'blog_post_title' => 'This is my blog post titled one',
            'blog_post_excerpt'=>'This is the blog post excerpt',
            'blog_post_content' => '<h1>This is my post title</h1>',
            'blog_post_person' => $u->getAttribute('person_slug'),
            'categories' => ['cat111'],
            'published_at' => '201902051959',
            'tags' => ['tag1', 'tag2', 'tag3']
        ];
        $this->postJson(
            '/ajax/admin/blog/post/create',
            $blogPostData);
        Bus::fake();
        $blogPostData = [
            'blog_status' => 'BLOG_STATUS_DRAFT',
            'blog_post_title' => 'This is my blog post title',
            'blog_post_excerpt'=>'This is the blog post excerpt',
            'blog_post_content' => '<h1>This is my post title</h1>',
            'blog_post_person' => $u->getAttribute('person_slug'),
            'categories' => ['cat111'],
            'published_at' => '201902051959',
            'tags' => ['tag1', 'tag2', 'tag3']
        ];
        $this->postJson(
            '/ajax/admin/blog/post/create',
            $blogPostData);
        Bus::assertDispatched(UpdateElasticsearch::class, function ($job) use ($blogPostData) {
            $a1 = $job->blogPostData instanceof BlogPost;
            $a2 = isset($job->requestData->person_id);
            $a3 = isset($job->requestData->blog_post_title);
            $a4 = !is_null($job->blogPostData->getAttribute('entity_type_id'));
            return $a1&&$a2&&$a3&&$a4;
        });
    }


}
