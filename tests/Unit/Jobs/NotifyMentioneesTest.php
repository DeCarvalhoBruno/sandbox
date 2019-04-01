<?php

namespace Tests\Unit;

use App\Models\Entity;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Redis;
use Mockery as m;
use Naraki\Blog\Models\BlogPost;
use Naraki\Forum\Emails\Mention;
use Naraki\Forum\Jobs\NotifyMentionees;
use Naraki\Mail\Jobs\SendMail;
use Tests\TestCase;

class NotifyMentioneesTest extends TestCase
{
    use DatabaseMigrations, WithoutMiddleware;

    public function test_notify_mentionees_()
    {
        $u = $this->createUser();
        $this->signIn($u);
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

        $u = $this->createUser(3);
        $firstUser = array_shift($u);
        $userNames = [];
        foreach ($u as $user) {
            $userNames[] = $user->getAttribute('username');
        }
//You were mentioned in a comment under blog post XXXX by ZZZZZ
        $this->signIn();
        Redis::shouldReceive('hgetall')->andReturns([
            'reply'=>true,
            'mention'=>true
        ]);
        $mockSocialiteUser = m::mock('overload:'.SendMail::class)
            ->shouldReceive('__construct')
            ->with(m::mock(Mention::class));
        $j = new NotifyMentionees(
            $userNames, $firstUser,(object)[],'john_doe-dfhdjkfhdj');
        $j->handle();
    }

}
