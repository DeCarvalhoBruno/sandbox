<?php

namespace Tests\Feature\Frontend;

use App\Models\Email\EmailList;
use App\Models\Email\EmailSubscriber;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use DatabaseMigrations, WithoutMiddleware;

    public function test_edit_emailing_lists()
    {
        $t = $this->createUser();
        $u = $this->signIn($t);
        $response = $this->post('settings/profile/update', [
            'notifications' => [
                EmailList::NEWSLETTERS => 'on'
            ]
        ]);
        $response->assertStatus(302);
        $this->assertEquals(1, count(EmailSubscriber::query()->get()->toArray()));
        $response = $this->post('settings/profile/update', []);
        $response->assertStatus(302);
        $this->assertEquals(0, count(EmailSubscriber::query()->get()->toArray()));
    }

}