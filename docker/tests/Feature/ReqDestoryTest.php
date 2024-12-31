<?php

namespace Tests\Feature;

use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Exceptions;

class ReqDestoryTest extends TestCase
{
    use RefreshDatabase;
    public function test_not_found(): void
    {
        $response = $this->delete('/r/Account/1');
        $response->assertStatus(500);
    }
}
