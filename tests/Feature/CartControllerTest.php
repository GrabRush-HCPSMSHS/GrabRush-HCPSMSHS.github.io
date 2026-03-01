<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private User $anotherCustomer;
    private User $admin;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create(['role' => UserRole::Customer]);
        $this->anotherCustomer = User::factory()->create(['role' => UserRole::Customer]);
        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
        $this->product = Product::factory()->create(['quantity' => 10, 'is_available' => true]);
    }

    // Auth tests
    public function test_unauthenticated_user_is_redirected(): void
    {
        $this->get(route('customer.cart-items.index'))->assertRedirect(route('login'));
    }

    public function test_non_customer_is_forbidden(): void
    {
        $this->actingAs($this->admin)->get(route('customer.cart-items.index'))
            ->assertRedirect(route('admin.dashboard'));
    }

    // Index tests
    public function test_customer_can_view_their_cart(): void
    {
        CartItem::factory()->create(['user_id' => $this->customer->id, 'product_id' => $this->product->id]);

        $this->actingAs($this->customer)
            ->get(route('customer.cart-items.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Customer/Cart')
                ->has('cartItems', 1)
            );
    }

    public function test_index_handles_service_exception(): void
    {
        $this->mock(CartService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getUserCartItems')->andThrow(new \Exception('Service Error'));
        });

        $this->actingAs($this->customer)
            ->get(route('customer.cart-items.index'))
            ->assertRedirect(route('customer.cart-items.index'))
            ->assertSessionHasErrors('order_error');
    }

    // Store tests
    public function test_customer_can_add_item_to_cart(): void
    {
        $cartData = [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ];

        $this->actingAs($this->customer)
            ->post(route('customer.cart-items.store'), $cartData)
            ->assertRedirect(route('customer.products.show', $this->product->id));

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);
    }

    // Update tests
    public function test_customer_can_update_cart_item_quantity(): void
    {
        $cartItem = CartItem::factory()->create([
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $this->actingAs($this->customer)
            ->put(route('customer.cart-items.update', $cartItem), [
                'product_id' => $cartItem->product_id,
                'quantity' => 5,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('cart_items', ['id' => $cartItem->id, 'quantity' => 5]);
    }

    public function test_customer_cannot_update_another_users_cart_item(): void
    {
        $cartItem = CartItem::factory()->create([
            'user_id' => $this->anotherCustomer->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        // With the fix to StoreCartItemRequest, this should now be forbidden
        $this->actingAs($this->customer)
            ->put(route('customer.cart-items.update', $cartItem), [
                'product_id' => $cartItem->product_id, // Pass validation
                'quantity' => 5,
            ])
            ->assertForbidden();
    }

    // Destroy tests
    public function test_customer_can_delete_cart_item(): void
    {
        $cartItem = CartItem::factory()->create(['user_id' => $this->customer->id]);

        $this->actingAs($this->customer)
            ->delete(route('customer.cart-items.destroy', $cartItem))
            ->assertRedirect();

        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
    }

    public function test_customer_cannot_delete_another_users_cart_item(): void
    {
        $cartItem = CartItem::factory()->create(['user_id' => $this->anotherCustomer->id]);

        $this->actingAs($this->customer)
            ->delete(route('customer.cart-items.destroy', $cartItem))
            ->assertForbidden(); // Gate should forbid this
    }

    // Bulk Destroy tests
    public function test_customer_can_bulk_delete_cart_items(): void
    {
        $myCartItem1 = CartItem::factory()->create(['user_id' => $this->customer->id]);
        $myCartItem2 = CartItem::factory()->create(['user_id' => $this->customer->id]);
        $otherCartItem = CartItem::factory()->create(['user_id' => $this->anotherCustomer->id]);

        $idsToDelete = [$myCartItem1->id, $myCartItem2->id, $otherCartItem->id];

        $this->actingAs($this->customer)
            ->delete(route('customer.cart-items.bulk-destroy'), ['ids' => $idsToDelete])
            ->assertRedirect(route('customer.cart-items.index'));

        $this->assertDatabaseMissing('cart_items', ['id' => $myCartItem1->id]);
        $this->assertDatabaseMissing('cart_items', ['id' => $myCartItem2->id]);
        $this->assertDatabaseHas('cart_items', ['id' => $otherCartItem->id]); // Crucial check
    }
}
