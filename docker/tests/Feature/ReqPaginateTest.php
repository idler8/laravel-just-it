<?php

namespace Tests\Feature;

use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Exceptions;

class ReqPaginateTest extends TestCase
{
    use RefreshDatabase;
    public function test_per_page_1000(): void
    {
        Account::factory()->count(1001)->create();
        $response = $this->get('/r/Account/1001/1');
        $response->assertJsonCount(1000, 'data');
    }
}
