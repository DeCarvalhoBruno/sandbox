<?php

namespace Tests\Unit;

use Naraki\Sentry\Jobs\UpdateUserElasticsearch;
use Naraki\Sentry\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Naraki\Elasticsearch\Facades\ElasticSearchIndex;
use Tests\TestCase;

class UpdateUserElasticSearchTest extends TestCase
{
    use DatabaseMigrations, WithoutMiddleware;

    public function test_update_user_elasticsearch_user_creation()
    {
        ElasticSearchIndex::shouldReceive('index')->times(1);
        $this->post('register', [
            'first_name' => 'First name',
            'username' => 'brian_campbell',
            'email' => 'brian.campbell@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $es = new UpdateUserElasticsearch(
            UpdateUserElasticsearch::WRITE_MODE_CREATE,
            User::query()->where('email','brian.campbell@example.com')
                ->first()->getKey()
        );

        $es->handle();
        $this->assertArrayHasKey('username',$es->documentContents);
        $this->assertArrayHasKey('email',$es->documentContents);
        $this->assertArrayHasKey('full_name',$es->documentContents);

    }

}
