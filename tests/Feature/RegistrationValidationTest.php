<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RegistrationValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_requires_email_tld()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'ish@gmail', // Missing TLD
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => true,
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_registration_accepts_valid_email_tld()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'ish@gmail.com', // Valid TLD
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => true,
        ]);

        $response->assertSessionHasNoErrors();
    }
}
