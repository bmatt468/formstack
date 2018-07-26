<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthFailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAuthFail()
    {
        $response = $this->get('api/documents');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'status' => 401,
            ]);
    }
}
