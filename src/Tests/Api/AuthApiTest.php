<?php

namespace App\Tests\Api;

use App\Tests\AbstractApiTestCase;

class AuthApiTest extends AbstractApiTestCase
{
    public function testLogin(): void
    {

        $response = $this->put('token', [
            'username' => 'user1@gmail.com',
            'password' => 'test',
        ]);


        // Validate a successful response and some content
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Hello World');
    }
}
