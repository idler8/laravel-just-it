<?php

namespace Tests\Feature;

use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Exceptions;

class BaseRESTfulTest extends TestCase
{
    use RefreshDatabase;
    public function test_index(): void
    {
        Account::factory()->count(10)->create();
        $response = $this->get('/r/Account');
        $response->assertJsonCount(10);
        print 'foo';
    }
    public function test_paginate(): void
    {
        Account::factory()->count(20)->create();
        $response = $this->get('/r/Account/10/1');
        $response->assertJsonPath('total', 20);
        $response->assertJsonPath('per_page', 10);
        $response->assertJsonPath('current_page', 1);
    }
    public function test_CRUD(): void
    {
        Exceptions::fake();
        $this->post('/r/Account', [
            'name' => 'world',
            'password' => 'password'
        ]);

        $response = $this->get('/r/Account/1');
        $response->assertJsonPath('name', 'world');

        $this->put('/r/Account/1', ['name' => 'changed']);
        $response = $this->get('/r/Account/1');
        $response->assertJsonPath('name', 'changed');

        $this->delete('/r/Account/1');
        $response = $this->get('/r/Account');
        $response->assertContent('[]');
    }
}
