<?php

namespace Tests\Feature;

use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Exceptions;

class ReqUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_auto_fill(): void
    {
        Account::create(['name' => 'builder', 'password' => 'password']);
        $original = $this->get('/r/Account');

        $response = $this->put('/r/Account/1', ['id' => 8, 'nonexistent' => 'value']);
        $response->assertStatus(200);
        $response = $this->get('/r/Account');
        $this->assertSame($response->getContent(), $original->getContent());
    }
}
