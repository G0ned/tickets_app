<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_user_is_not_default_admin(): void 
    {
        //Arrange
        $user = new User();
        $user->is_admin = false;
        
        //Assert
        $this->assertFalse($user->is_admin);
    }

    public function test_user_is_admin(): void 
    {
        //Arrange
        $user = new User();
        $user->is_admin = true;
        
        //Assert
        $this->assertTrue($user->is_admin);
    }
}
