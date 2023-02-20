<?php

namespace Tests\Feature;

use Tests\BaseTestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->payload = [
            'email' => 'qwe@qwe', 
            'password' => 'qweqweqwe', 
            'role' => User::ROLE_USER,
        ]; 

        $this->user = User::factory([
            'email' => $this->payload['email'], 
            'password' => Hash::make($this->payload['password']), 
            'role' => $this->payload['role'],
        ])->create();

        $this->endpoint = '/api/user/login';
    }

    public function test_user_creates_new_request_to_get_vacation()
    {
        $response = $this->post($this->endpoint, $this->payload);
        
        $response->assertStatus(200)->assertJsonStructure([
            'user' => [
                'id', 'name', 'email', 'role', 'available_vacation_days', 'updated_at', 'created_at',  
            ],
            'token'
        ]);

    }
}
