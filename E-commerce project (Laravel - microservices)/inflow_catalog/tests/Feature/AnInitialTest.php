<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AnInitialTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->url = config('app.url') . '/api/v1';
    }

    /**
     * A basic feature test example.
     */
    public function test_shop_list(): void
    {
        $response = $this->get($this->url . '/stores');

        $response->assertStatus(200);
    }
}
