<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Justit\Resource;
use Illuminate\Testing\AssertableJsonString;
use Justit\ApiDocument;

class ApiDocumentTest extends TestCase
{
    use RefreshDatabase;
    public function test_resource_document(): void
    {
        $apiDocuments = Resource::document();
        $this->assertCount(2, $apiDocuments->toArray());
        $this->assertContains('Account', $apiDocuments->pluck('key')->toArray());
        $this->assertContains('Account.Post', $apiDocuments->pluck('key')->toArray());
        (new AssertableJsonString($apiDocuments))->assertStructure([
            '*' => [
                'key',
                'name',
                'parameters' => ['*' => ['key']],
                'relations' => ['*' => ['key', 'name']]
            ],
        ]);
    }
    public function test_api_document(): void
    {
        $apiDocuments = ApiDocument::document('api');
        $this->assertCount(6, $apiDocuments->toArray());
        $this->assertContains('Justit\Controller@index', $apiDocuments->pluck('key')->toArray());
        $this->assertContains('Justit\Controller@paginate', $apiDocuments->pluck('key')->toArray());
        $this->assertContains('Justit\Controller@show', $apiDocuments->pluck('key')->toArray());
        $this->assertContains('Justit\Controller@update', $apiDocuments->pluck('key')->toArray());
        $this->assertContains('Justit\Controller@destroy', $apiDocuments->pluck('key')->toArray());
        $this->assertContains('Justit\Controller@store', $apiDocuments->pluck('key')->toArray());
        (new AssertableJsonString($apiDocuments))->assertStructure([
            '*' => [
                'key',
                'name',
                'parameters' => ['*' => ['key']],
                'urls'
            ],
        ]);
    }
}
