<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase 
{
    public function test_that_the_application_returns_succesful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}