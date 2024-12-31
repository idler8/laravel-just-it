<?php

namespace Tests\Feature;

use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Exceptions;

class ReqIndexTest extends TestCase
{
    use RefreshDatabase;
    public function test_max_count_1000(): void
    {
        Account::factory()->count(1001)->create();
        $response = $this->get('/r/Account');
        $response->assertJsonCount(1000);
    }
    public function test_default_desc(): void
    {
        Account::factory()->count(10)->create();
        $response = $this->get('/r/Account');
        $response->assertJsonPath('0.id', 10);
        $response->assertJsonPath('9.id', 1);
    }
}
