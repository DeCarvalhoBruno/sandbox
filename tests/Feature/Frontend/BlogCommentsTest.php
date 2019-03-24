<?php

namespace Tests\Feature\Frontend;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Validation\ValidationException;
use Naraki\Elasticsearch\Facades\ElasticSearchIndex;
use Naraki\Forum\Models\ForumPost;
use Naraki\Forum\Requests\CreateComment;
use Tests\TestCase;

class BlogCommentsTest extends TestCase
{
    use DatabaseMigrations, WithoutMiddleware;

    private function createBlogPost()
    {
        $u = $this->createUser();
        $this->signIn($u);
        ElasticSearchIndex::shouldReceive('index')->times(1);
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
        $this->createBlogPost();
        $this->assertEquals(0, ForumPost::query()->count());
        $txt = '<h2 style="font-size: 3rem;padding: 3rem;color:#fff">
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
          <script>window.location.href=\'http://www.myalternatesite.com\'</script>
          <blockquote>
            It\'s amazing
            <br />
            – mom
          </blockquote>';
        $this->postJson('/ajax/forum/blog_posts/my-blog-post/comment',
            ['txt' => $txt]
        );
        $posts = ForumPost::query()->get();
        $this->assertEquals(1, $posts->count());
        $this->assertNotContains('myalternatesite.com', $posts[0]->getAttribute('forum_post'));
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
        $this->createBlogPost();
        $this->postJson('/ajax/forum/blog_posts/my-blog-post/comment',
            ['txt' => 'my comment']
        );
        $commentSlug = ForumPost::query()->first()->getSlugColumn();
        $this->postJson('/ajax/forum/blog_posts/my-blog-post/comment',
            ['txt' => 'my comment in reply to', 'reply_to' => $commentSlug]
        );
        $this->assertEquals(2, ForumPost::query()->count());
        $this->assertNotNull(ForumPost::query()->get()->last()->getParentId());
    }

}