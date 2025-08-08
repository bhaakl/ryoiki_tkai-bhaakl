<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->url = config('app.url') . '/api/v1';
        $this->tenant->makeCurrent();
    }

    /**
     * A basic feature test example.
     */
    public function test_product_list(): void
    {
        $response = $this->get($this->url . '/products');
        $response->assertStatus(200);
        $response = $this->get($this->url . '/products', [
            'category' => Category::all()->random()->id
        ]);
        $response->assertStatus(200);
        $response = $this->get($this->url . '/products', [
            'price_from' => 1000
        ]);
        $response->assertStatus(200);
        $response = $this->get($this->url . '/products', [
            'price_to' => 1000
        ]);
        $response->assertStatus(200);
        $response = $this->get($this->url . '/products', [
            'search' => 'test'
        ]);
        $response->assertStatus(200);
    }

    public function test_product_search(): void
    {
        $response = $this->get($this->url . '/products/search?search=dolor&limit=10');
        $response->assertStatus(200)->assertDontSee('total_pages');

        $response = $this->get($this->url . '/products/search?search=dolor');
        $response->assertStatus(200)->assertSee('total_pages');
    }

    public function test_product_refresh(): void
    {
        $response = $this->post($this->url . '/products/refresh', [
            'products' => Product::limit(10)->inRandomOrder()->pluck('id')->toArray()
        ]);
        $response->assertStatus(200);
    }

    //TODO: continue
}
