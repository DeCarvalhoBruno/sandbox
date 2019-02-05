<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class BlogCategoryTest extends TestCase
{
    use DatabaseMigrations, WithoutMiddleware;

    public function test_create()
    {
        $response = $this->postJson(
            "/ajax/admin/blog/categories/create",
            ["label" => 'dfhaljhfjh', 'parent' => '']);
        $response->assertStatus(204);

    }

    public function test_show()
    {
        $response = $this->getJson('/ajax/admin/blog/posts');
        $response->assertStatus(200);

    }

}