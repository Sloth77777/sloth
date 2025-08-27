<?php

namespace Tests\Parts\API\Controllers;

use App\Models\Category\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

uses(RefreshDatabase::class);

test('index method returns paginated categories with correct structure', function () {
    Category::factory()->count(15)->create();

    $response = $this->getJson(route('categories.index'));

    $response->assertOk()
        ->assertJson(fn(AssertableJson $json) => $json->has('data', 10)
        ->has('links')
            ->has('meta', fn($meta) => $meta->has('current_page')
                ->has('last_page')
                ->has('per_page')
                ->has('total')
            )
        );
});

test('index method returns empty data when no categories exist', function () {
    $response = $this->getJson(route('categories.index'));

    $response->assertOk()
        ->assertJson(fn(AssertableJson $json) => $json->has('data', 0)
            ->has('links')
            ->has('meta', fn($meta) => $meta->where('total', 0)
                ->etc()
            )
        );
});
