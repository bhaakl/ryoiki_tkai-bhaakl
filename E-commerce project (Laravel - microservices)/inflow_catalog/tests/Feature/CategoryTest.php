<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->url = config('app.url') . '/api/v1';
        $this->tenant->makeCurrent();
    }

    public function test_categories_list(): void
    {
        $response = $this->get($this->url . '/categories');

        $response->assertStatus(200);
    }

    public function test_categories_show(): void
    {
        $category = Category::first();
        $response = $this->get($this->url . '/categories/' . $category->id);

        $response->assertStatus(200);
    }
}
