<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Account\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Arr;
use Justit\Resource;

class QueryTest extends TestCase
{
    use RefreshDatabase;
    public function test_select(): void
    {
        Account::factory()->count(10)->create();
        $key = Resource::keyUsedSelect;
        $response = $this->get('/r/Account?' . Arr::query([$key => ['id']]));
        $response->assertExactJsonStructure(['*' => ['id']]);

        $response = $this->get('/r/Account?' . Arr::query([$key => ['id', 'name']]));
        $response->assertExactJsonStructure(['*' => ['id', 'name']]);
    }
    public function test_where_range(): void
    {
        Account::factory()->count(10)->create();
        $idKey = Resource::keyUsedRange . 'id';
        $response = $this->get('/r/Account?' . Arr::query([$idKey => [5]]));
        $response->assertJsonCount(6);
        $response = $this->get('/r/Account?' . Arr::query([$idKey => [null, 5]]));
        $response->assertJsonCount(5);
        $response = $this->get('/r/Account?' . Arr::query([$idKey => [5, 6]]));
        $response->assertJsonCount(2);
    }
    public function test_relation(): void
    {
        Account::factory()->count(1)->create();
        Post::create(['account_id' => 1]);
        $key = Resource::keyUsedRelation;
        $response = $this->get('/r/Account?' . Arr::query([$key => ['posts']]));
        $response->assertJsonCount(1, '0.posts');
        $response->assertJsonPath('0.posts.0.id', 1);
    }
    public function test_where_eq(): void
    {
        Account::factory()->count(10)->create();
        $response = $this->get('/r/Account?' . Arr::query(['id' => 5]));
        $response->assertJsonCount(1);
        $response->assertJsonPath('0.id', 5);
    }
    public function test_where_in(): void
    {
        Account::factory()->count(10)->create();
        $response = $this->get('/r/Account?' . Arr::query(['id' => [1, 5]]));
        $response->assertJsonCount(2);
        $response->assertJsonPath('0.id', 5);
        $response->assertJsonPath('1.id', 1);
    }
    public function test_where_like(): void
    {
        Account::factory()->count(20)->create();
        $response = $this->get('/r/Account?' . Arr::query(['id' => '2%']));
        $response->assertJsonCount(2);
        $response->assertJsonPath('0.id', 20);
        $response->assertJsonPath('1.id', 2);
        $response = $this->get('/r/Account?' . Arr::query(['id' => '%2']));
        $response->assertJsonCount(2);
        $response->assertJsonPath('0.id', 12);
        $response->assertJsonPath('1.id', 2);
        $response = $this->get('/r/Account?' . Arr::query(['id' => '%2%']));
        $response->assertJsonCount(3);
        $response->assertJsonPath('0.id', 20);
        $response->assertJsonPath('1.id', 12);
        $response->assertJsonPath('2.id', 2);
    }
    public function test_scope_custom(): void
    {
        Account::factory()->count(12)->create();
        $response = $this->get('/r/Account?' . Arr::query([':half_middle' => 1]));
        $response->assertJsonCount(6);
        $response->assertJsonPath('0.id', 9);
        $response->assertJsonPath('5.id', 4);
    }
}
