<?php

namespace Tests\Unit;

use App\Models\Entity;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Redis;
use Naraki\Forum\Jobs\NotifyMentionees;
use Tests\TestCase;

class NotifyMentioneesTest extends TestCase
{
    use DatabaseMigrations, WithoutMiddleware;

    public function test_notify_mentionees_()
    {
        $u = $this->createUser(3);
        $firstUser = array_shift($u);
        $userNames = [];
        foreach ($u as $user) {
            $userNames[] = $user->getAttribute('username');
        }

        $this->signIn();
        Redis::shouldReceive('hgetall')->andReturns([
            'reply'=>true,
            'mention'=>true
        ]);
        $j = new NotifyMentionees($userNames, 'comment', $firstUser,Entity::BLOG_POSTS,12);
$j->handle();
    }

}
