<?php

namespace Tests\Feature;

use Tests\BaseTestCase;
use App\Models\User;

class RegisterTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->payload = [
            'name' => 'qwe', 
            'email' => 'qwe@qwe', 
            'password' => 'qweqweqwe', 
            'role' => User::ROLE_USER,
        ]; 

        $this->endpoint = '/api/user/register';
    }

    public function test_user_creates_new_request_to_get_vacation()
    {
        $response = $this->post($this->endpoint, $this->payload);
        
        $response->assertStatus(201)->assertJsonStructure([
            'user' => [
                'id', 'name', 'email', 'role', 'available_vacation_days', 'updated_at', 'created_at',  
            ],
            'token'
        ]);

        $this->assertDatabaseHas((new User())->getTable(), [
            'email' => $this->payload['email'],
        ]);

    }
}
