<?php

namespace Tests\Feature\Admin;

use Naraki\Blog\Facades\Blog;
use Naraki\Blog\Models\BlogCategory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class BlogCategoryTest extends TestCase
{
    use DatabaseMigrations, WithoutMiddleware;

    public function test_create_category()
    {
        $str = 'First root node';
        $response = $this->postJson(
            '/ajax/admin/blog/categories',
            ['label' => $str, 'parent' => '']);
        $response->assertStatus(200)
            ->assertJson(['id' => slugify($str)]);
        $str2='Second node';
        $this->postJson(
            '/ajax/admin/blog/categories',
            ['label' => $str2, 'parent' => slugify($str)]);
        $this->assertEquals(BlogCategory::query()->get()->count(), 3);
        //Testing whether the child category's parent is the one we specified
        $this->assertEquals(
            Blog::category()->getCat(slugify($str2))->getAttribute('parent_id'),
            Blog::category()->getCat(slugify($str))->getAttribute(Blog::category()->getKeyName())
        );
    }

    public function test_show()
    {
        $response = $this->getJson('/ajax/admin/blog/categories');
        $response->assertStatus(200);
    }

    public function test_delete()
    {
        $str = 'First root node';
        $this->postJson(
            '/ajax/admin/blog/categories',
            ['label' => $str, 'parent' => '']);
        $this->assertEquals(BlogCategory::query()->get()->count(), 2);
        $response = $this->deleteJson(
            "/ajax/admin/blog/categories/".slugify($str));
        $response->assertStatus(204);
        $this->assertEquals(BlogCategory::query()->get()->count(), 1);
    }

}