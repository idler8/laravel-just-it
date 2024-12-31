<?php

namespace Tests\Feature;

use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Exceptions;

class ReqShowTest extends TestCase
{
    use RefreshDatabase;
    public function test_first(): void
    {
        Account::factory()->count(10)->create();
        $response = $this->get('/r/Account/-');
        $response->assertJsonPath('id', 1);
    }
    public function test_last(): void
    {
        Account::factory()->count(10)->create();
        $response = $this->get('/r/Account/+');
        $response->assertJsonPath('id', 10);
    }
}
