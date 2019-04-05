<?php

namespace Tests\Feature\Frontend;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Naraki\Elasticsearch\Facades\ElasticSearchIndex;
use Naraki\Forum\Facades\Forum;
use Naraki\Forum\Models\ForumPost;
use Naraki\Forum\Requests\CreateComment;
use Tests\TestCase;

class BlogCommentsTest extends TestCase
{
    use DatabaseMigrations, WithoutMiddleware;

    private function createBlogPost($withJobs = false)
    {
        $u = $this->createUser();
        $this->signIn($u);
        if ($withJobs) {
            ElasticSearchIndex::shouldReceive('index')->times(1);
        }
        $this->postJson(
            "/ajax/admin/blog/post/create",
            [
                'blog_status' => "BLOG_STATUS_DRAFT",
                'blog_post_title' => "my blog post",
                'blog_post_person' => $u->getAttribute('person_slug'),
                'categories' => [],
                'published_at' => "201902051959",
                'tags' => []
            ]);

        return $u;
    }

    public function test_blog_comments_post_comment()
    {
        $this->createBlogPost(true);
        $this->assertEquals(0, ForumPost::query()->count());
        $u = $this->createUser();
        $txt = sprintf('<h2 style="font-size: 3rem;padding: 3rem;color:#fff">
            Hi there,
          </h2>
          <p>
            this is a very <em>basic</em> example of tiptap.
          </p>
          <pre><code>window.location.href=\'http://test.com\'</code></pre>
          <ul>
            <li>
              A regular list
            </li>
            <li>
              With regular items
            </li>
          </ul>
          <p>It\'s a lot of <span class="mention" 
          data-mention-id="%1$s" 
          contenteditable="false">@%1$s</span> for the rest of us. I don\'t</p>
          <script>window.location.href=\'http://www.myalternatesite.com\'</script>
          <blockquote>
            It\'s amazing
            <br />
            – mom
          </blockquote>', $u->getAttribute('username'));

        Session::shouldReceive('has')->times(1);
        Session::shouldReceive('put')->times(1);
        Redis::shouldReceive('hgetall')->andReturns([
            'reply' => true,
            'mention' => true
        ]);

//        Bus::shouldReceive('dispatch');
        $this->postJson('/ajax/forum/blog_posts/my-blog-post/comment',
            ['txt' => $txt]
        );
        $posts = ForumPost::query()->get();
        $this->assertEquals(1, $posts->count());
        $this->assertNotContains('myalternatesite.com', $posts[0]->getAttribute('forum_post'));
    }

    public function test_blog_comments_update()
    {
        $this->withoutJobs();
        $this->createBlogPost();
        Session::shouldReceive('has')->times(2);
        Session::shouldReceive('put')->times(2);
        $this->postJson('/ajax/forum/blog_posts/my-blog-post/comment',
            ['txt' => 'my comment']
        );
        $commentSlug = ForumPost::query()->first()->getSlug();
        $txt = 'my updated comment';
        $this->patchJson('/ajax/forum/blog_posts/my-blog-post/comment',
            ['txt' => $txt, 'reply_to' => $commentSlug]
        );
        $this->assertEquals($txt, $commentSlug = ForumPost::query()->first()->getAttribute('forum_post'));

    }

    public function test_blog_comments_favorite()
    {
        $this->withoutJobs();
        $this->createBlogPost();
        $this->postJson('/ajax/forum/blog_posts/my-blog-post/comment',
            ['txt' => 'my comment']
        );
        $commentSlug = ForumPost::query()->first()->getSlug();

        Forum::shouldReceive('post->favorite')->with($commentSlug);
        $this->patchJson('/ajax/forum/blog_posts/my-blog-post/favorite/' . $commentSlug,
            ['txt' => 'my comment']
        );
    }

    public function test_blog_comments_post_comment_too_long()
    {
        $u = $this->createUser();
        $this->signIn($u);
        $this->expectException(ValidationException::class);
        $this->postJson('/ajax/forum/blog_posts/my-blog-post/comment',
            ['txt' => str_repeat('string', CreateComment::$characterLimit / 5)]
        );
    }

    public function test_blog_comments_post_comment_reply()
    {
        $this->withoutJobs();
        $this->createBlogPost();
        Session::shouldReceive('has')->times(2);
        Session::shouldReceive('put')->times(2);
        $this->postJson('/ajax/forum/blog_posts/my-blog-post/comment',
            ['txt' => 'my comment']
        );
        $commentSlug = ForumPost::query()->first()->getSlug();
        $u = $this->createUser();
        $this->signIn($u);
        $this->postJson('/ajax/forum/blog_posts/my-blog-post/comment',
            ['txt' => 'my comment in reply to', 'reply_to' => $commentSlug]
        );
        $this->assertEquals(2, ForumPost::query()->count());
        $this->assertNotNull(ForumPost::query()->get()->last()->getParentId());
    }

    public function test_blog_comments_post_comment_delete()
    {
        $this->withoutJobs();
        $this->createBlogPost();
        $this->postJson('/ajax/forum/blog_posts/my-blog-post/comment',
            ['txt' => 'my comment']
        );
        $commentSlug = ForumPost::query()->first()->getSlug();
        $this->assertEquals(1, ForumPost::query()->count());
        $this->deleteJson('/ajax/forum/blog_posts/my-blog-post/comment/' . $commentSlug);
        $this->assertEquals(0, ForumPost::query()->count());
    }

    public function test_blog_comments_update_notifications()
    {
        $u = $this->createUser();
        $this->signIn($u);
        Redis::shouldReceive('hgetall')->shouldReceive('hmset')->with(
            sprintf('comment_notif_opt.%s', $u->getKey()),
            ['reply' => true]
        );
        $this->patchJson('/ajax/forum/blog_posts/options',
            [
                'option' => 'reply',
                'flag' => true
            ]
        );
    }

}