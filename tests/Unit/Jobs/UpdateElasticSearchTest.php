<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Naraki\Blog\Job\DeleteElasticsearch;
use Naraki\Blog\Job\UpdateElasticsearch;
use Naraki\Blog\Models\BlogPost;
use Naraki\Elasticsearch\Facades\ElasticSearchIndex;
use Tests\TestCase;

class UpdateElasticSearchTest extends TestCase
{
    use DatabaseMigrations, WithoutMiddleware;

    public function test_class()
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
        $title = 'This is my blog post titled one';
        $blogPostData = [
            'blog_status' => 'BLOG_STATUS_DRAFT',
            'blog_post_title' => $title,
            'blog_post_excerpt' => 'This is the blog post excerpt',
            'blog_post_content' => '<h1>This is my post title</h1>',
            'blog_post_person' => $u->getAttribute('person_slug'),
            'categories' => ['cat111'],
            'published_at' => '201902051959',
            'tags' => ['tag1', 'tag2', 'tag3']
        ];
        $this->postJson(
            '/ajax/admin/blog/post/create',
            $blogPostData);

          $blogPost = BlogPost::query()
            ->where('blog_post_title', $title)
            ->scopes(['entityType'])->first();

        $es = new UpdateElasticsearch(UpdateElasticsearch::WRITE_MODE_CREATE,
            $blogPost,
            (object)[
                'blog_post_title' => 'title',
                'person_id' => $u->getAttribute('person_id')
            ],
            (object)['added' => ['tag1', 'tag2']],
            false
        );

        $es->handle();

        $this->assertArrayHasKey('url',$es->documentContents['meta']);
        $this->assertArrayHasKey('author',$es->documentContents['meta']);

    }

    public function test_create_document()
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
            'blog_post_excerpt' => 'This is the blog post excerpt',
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
            'blog_post_excerpt' => 'This is the blog post excerpt',
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
            return $a1 && $a2 && $a3 && $a4;
        });
    }

    public function test_update_document()
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
        $postTitle = 'This is my blog post titled one';
        $blogPostData = [
            'blog_status' => 'BLOG_STATUS_DRAFT',
            'blog_post_title' => $postTitle,
            'blog_post_excerpt' => 'This is the blog post excerpt',
            'blog_post_content' => '<h1>This is my post title</h1>',
            'blog_post_person' => $u->getAttribute('person_slug'),
            'categories' => ['cat111'],
            'published_at' => '201902051959',
            'tags' => ['tag1', 'tag2', 'tag3']
        ];
        $this->postJson(
            '/ajax/admin/blog/post/create',
            $blogPostData);

        $postTitle2 = 'This is my blog post that I have just updated';
        ElasticSearchIndex::shouldReceive('update')->once();
        $this->postJson(
            "/ajax/admin/blog/post/edit/" . slugify($postTitle),
            [
                'blog_post_title' => $postTitle2,
            ]
        );

        Bus::fake();
        $this->postJson(
            "/ajax/admin/blog/post/edit/" . slugify($postTitle),
            [
                'blog_post_title' => 'This is my blog post that I have just updated a second time',
            ]
        );

        Bus::assertDispatched(UpdateElasticsearch::class, function ($job) use ($blogPostData) {
            $a1 = $job->blogPostData instanceof BlogPost;
            $a2 = !isset($job->requestData->person_id);
            $a3 = isset($job->requestData->blog_post_title);
            $a4 = !is_null($job->blogPostData->getAttribute('entity_type_id'));
            return $a1 && $a2 && $a3 && $a4;
        });
    }

    public function test_delete()
    {
        $u = $this->createUser();

        $postTitle = 'title1';
        $blogPostData = [
            'blog_status' => 'BLOG_STATUS_DRAFT',
            'blog_post_title' => $postTitle,
            'blog_post_excerpt' => 'This is the blog post excerpt',
            'blog_post_content' => '<h1>This is my post title</h1>',
            'blog_post_person' => $u->getAttribute('person_slug'),
            'published_at' => '201902051959'
        ];
        $this->postJson(
            '/ajax/admin/blog/post/create',
            $blogPostData);

        $blogPostData['blog_post_title'] = 'title2';
        $this->postJson(
            '/ajax/admin/blog/post/create',
            $blogPostData);

        $blogPostData['blog_post_title'] = 'title3';
        $this->postJson(
            '/ajax/admin/blog/post/create',
            $blogPostData);

        $blogPostData['blog_post_title'] = 'title4';
        $this->postJson(
            '/ajax/admin/blog/post/create',
            $blogPostData);

        Bus::fake();
        $this->deleteJson('/ajax/admin/blog/post/title1');
        Bus::assertDispatched(DeleteElasticsearch::class, function ($job) use ($blogPostData) {
            $a1 = $job->blogPostData instanceof Collection;
            $a2 = $job->blogPostData[0] instanceof BlogPost;
            return $a1 && $a2;
        });
        Bus::fake();
        $this->postJson('/ajax/admin/blog/post/batch/delete',['posts'=>['title4','title3']]);
        Bus::assertDispatched(DeleteElasticsearch::class, function ($job) use ($blogPostData) {
            $a1 = $job->blogPostData instanceof Collection;
            $a2 = $job->blogPostData->count()==2;
            return $a1 && $a2;
        });
    }

}
