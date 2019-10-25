<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testRoutes()
    {
        echo   PHP_EOL . 'Testing login page: '. PHP_EOL;
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function testlogin() {
        echo   PHP_EOL . 'Testing form login: '. PHP_EOL;

        $credentials = [
            "email" => "xavierperna@gmail.com",
            "password" => "12341234"
        ];

        $response = $this->post('/login', $credentials);
        $response->assertRedirect('/');
        $this->assertCredentials($credentials);
    }

}
