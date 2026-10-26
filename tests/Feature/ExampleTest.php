<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertRedirect();
    }

    /**
     * Test that following the localization redirect leads to a successful response.
     */
    public function test_the_application_follows_localization_redirect_successfully(): void
    {
        $response = $this->followingRedirects()->get('/');

        $response->assertStatus(200);
    }
}
