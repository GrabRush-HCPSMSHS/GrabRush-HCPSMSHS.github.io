<?php

namespace Tests\Feature;

use App\Enums\PaymentMethod;
use App\Enums\UserRole;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\PaymentImage;
use App\Models\Product;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class CustomerOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private User $anotherCustomer;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create(['role' => UserRole::Customer]);
        $this->anotherCustomer = User::factory()->create(['role' => UserRole::Customer]);
        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
    }

    // Auth Tests
    public function test_unauthenticated_user_is_redirected(): void
    {
        $this->get(route('customer.orders.index'))->assertRedirect(route('login'));
    }

    public function test_non_customer_is_forbidden(): void
    {
        $this->actingAs($this->admin)->get(route('customer.orders.index'))
            ->assertRedirect(route('admin.dashboard'));
    }

    // Index Tests
    public function test_customer_can_view_their_orders(): void
    {
        Order::factory()->create(['user_id' => $this->customer->id]);
        Order::factory()->create(['user_id' => $this->anotherCustomer->id]);

        $this->actingAs($this->customer)
            ->get(route('customer.orders.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Customer/Orders')
                ->has('orders', 1)
                ->where('orders.0.user_id', $this->customer->id)
            );
    }

    // Store Tests
    public function test_customer_can_create_an_order_from_cart(): void
    {
        $product1 = Product::factory()->create(['quantity' => 10, 'is_available' => true]);
        $product2 = Product::factory()->create(['quantity' => 10, 'is_available' => true]);
        CartItem::factory()->create([
            'user_id' => $this->customer->id,
            'product_id' => $product1->id,
            'quantity' => 2,
        ]);
        CartItem::factory()->create([
            'user_id' => $this->customer->id,
            'product_id' => $product2->id,
            'quantity' => 3,
        ]);
        $paymentImage = PaymentImage::factory()->create([
            'user_id' => $this->customer->id,
            'order_id' => null, // Prevent creating an extra order
        ]);

        $this->mock(ProductService::class, function (MockInterface $mock) {
            $mock->shouldReceive('updateStock')->twice();
        });

        $this->actingAs($this->customer)
            ->post(route('customer.orders.store'), [
                'payment_image_id' => $paymentImage->id,
                'payment_method' => PaymentMethod::UploadReceipt->value,
            ])
            ->assertRedirect(route('customer.orders.index'));

        $this->assertDatabaseCount('orders', 1);
        $order = Order::first();
        $this->assertEquals($this->customer->id, $order->user_id);
        $this->assertEquals(PaymentMethod::UploadReceipt, $order->payment_method);

        $this->assertDatabaseCount('order_items', 2);
        $this->assertDatabaseHas('order_items', ['product_id' => $product1->id, 'quantity' => 2]);
        $this->assertDatabaseHas('order_items', ['product_id' => $product2->id, 'quantity' => 3]);

        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_customer_can_create_over_the_counter_order(): void
    {
        $product = Product::factory()->create(['quantity' => 10, 'is_available' => true]);
        CartItem::factory()->create([
            'user_id' => $this->customer->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->mock(ProductService::class, function (MockInterface $mock) {
            $mock->shouldReceive('updateStock')->once();
        });

        $this->actingAs($this->customer)
        ->post(route('customer.orders.store'), [
            'payment_method' => PaymentMethod::OverTheCounter->value,
            'notes' => 'Test notes',
        ])
        ->assertRedirect(route('customer.orders.index'));

        $this->assertDatabaseCount('orders', 1);
        $order = Order::first();
        $this->assertEquals($this->customer->id, $order->user_id);
        $this->assertEquals(PaymentMethod::OverTheCounter, $order->payment_method);
        $this->assertEquals('Test notes', $order->notes);
        $this->assertNull($order->payment_image_id);

        $this->assertDatabaseCount('order_items', 1);
        $this->assertDatabaseHas('order_items', ['product_id' => $product->id, 'quantity' => 2]);

        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_order_creation_fails_if_payment_image_is_invalid(): void
    {
        $this->actingAs($this->customer)
        ->post(route('customer.orders.store'), [
            'payment_image_id' => 999, // Invalid ID
            'payment_method' => PaymentMethod::UploadReceipt->value,
        ])
        ->assertSessionHasErrors('payment_image_id');
    }

    public function test_order_creation_requires_payment_image_id(): void
    {
        $this->actingAs($this->customer)
        ->post(route('customer.orders.store'), [
            'payment_method' => PaymentMethod::UploadReceipt->value,
        ])
        ->assertSessionHasErrors('payment_image_id');
    }
}
