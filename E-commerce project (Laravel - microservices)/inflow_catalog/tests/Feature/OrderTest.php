<?php


use App\Enums\DeliveryTypes;
use App\Models\Category;
use App\Models\Delivery;
use App\Models\DeliveryInterval;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
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
    public function testCreateOrder(): void
    {
        $delivery = Delivery::whereType(DeliveryTypes::PICKUP)->first()->id;
        $product = Product::active()->isOffer()->first()->id;
        $store = Store::first()->id;
        $interval = DeliveryInterval::first()->id;
        $response = $this->json('POST', $this->url . '/orders/create', [
            "user_id" => 1,
            "payment_system_id" => 1,
            "products" => [
                [
                    "id" => $product,
                    "quantity" => 2
                ]
            ],
            "delivery" => [
                "id" => $delivery,
                "store" => $store,
                "recipient_name" => "Test Recipient",
                "recipient_phone" => "Test phone number 1",
            ],
            "comment" => "This is a test comment"
        ]);
        $response->assertSuccessful();

        $response = $this->post($this->url . '/orders/create', [
            "payment_system_id" => 1,
            "user_id" => 2,
            "products" => [
                [
                    "id" => $product,
                    "quantity" => 2
                ]
            ],
            "delivery" => [
                "id" => $delivery,
                "recipient_name" => "Test Recipient",
                "recipient_phone" => "Test phone number 2",
            ],
            "comment" => "This is a test comment"
        ]);
        $response->assertStatus(422);

        $response = $this->post($this->url . '/orders/create', [
            "payment_system_id" => 1,
            "user_id" => 3,
            "products" => [
                [
                    "id" => $product,
                    "quantity" => 2
                ]
            ],
            "delivery" => [
                "id" => $delivery,
                "store" => $store,
                "recipient_phone" => "Test phone number 3",
            ],
            "comment" => "This is a test comment"
        ]);
        $response->assertStatus(422);

        $delivery = Delivery::whereType(DeliveryTypes::DELIVERY)->where('has_intervals', true)->first()->id;

        $response = $this->post($this->url . '/orders/create', [
            "payment_system_id" => 1,
            "user_id" => 4,
            "products" => [
                [
                    "id" => $product,
                    "quantity" => 2
                ]
            ],
            "delivery" => [
                "id" => $delivery,
                "date" => now()->addWeek()->format('Y-m-d'),
                "interval" => $interval,
                "address" => fake('ru_RU')->address,
                "recipient_name" => "Test Recipient",
                "recipient_phone" => "Test phone number 4",
                "recipient_note" => "This is a test comment"
            ],
            "comment" => "This is a test comment"
        ]);
        $response->assertSuccessful();

        $response = $this->post($this->url . '/orders/create', [
            "payment_system_id" => 1,
            "user_id" => 5,
            "products" => [
                [
                    "id" => $product,
                    "quantity" => 2
                ]
            ],
            "delivery" => [
                "id" => $delivery,
                "date" => now()->addWeek()->format('Y-m-d'),
                "interval" => $interval,
                "recipient_name" => "Test Recipient",
                "recipient_phone" => "Test phone number 5",
                "recipient_note" => "This is a test comment"
            ],
            "comment" => "This is a test comment"
        ]);
        $response->assertStatus(422);

        $response = $this->post($this->url . '/orders/create', [
            "payment_system_id" => 1,
            "user_id" => 6,
            "products" => [
                [
                    "id" => $product,
                    "quantity" => 2
                ]
            ],
            "delivery" => [
                "id" => $delivery,
                "date" => now()->addWeek()->format('Y-m-d'),
                "address" => fake('ru_RU')->address,
                "recipient_name" => "Test Recipient",
                "recipient_phone" => "Test phone number 6",
                "recipient_note" => "This is a test comment"
            ],
            "comment" => "This is a test comment"
        ]);
        $response->assertStatus(422);


        $delivery = Delivery::whereType(DeliveryTypes::DELIVERY)->where('has_intervals', false)->first()->id;

        $response = $this->post($this->url . '/orders/create', [
            "payment_system_id" => 1,
            "user_id" => 7,
            "products" => [
                [
                    "id" => $product,
                    "quantity" => 2
                ]
            ],
            "delivery" => [
                "id" => $delivery,
                "address" => fake('ru_RU')->address,
                "recipient_name" => "Test Recipient",
                "recipient_phone" => "Test phone number 7",
                "recipient_note" => "This is a test comment"
            ],
            "comment" => "This is a test comment"
        ]);
        $response->assertSuccessful();
    }
}
