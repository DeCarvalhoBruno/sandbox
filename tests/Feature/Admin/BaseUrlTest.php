<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;

class BaseUrlTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLoginUrl()
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
    }

}
