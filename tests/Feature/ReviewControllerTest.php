<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private User $anotherCustomer;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create(['role' => UserRole::Customer]);
        $this->anotherCustomer = User::factory()->create(['role' => UserRole::Customer]);
        $this->product = Product::factory()->create();
    }

    // Store Tests
    public function test_customer_can_create_a_review(): void
    {
        $reviewData = [
            'rating' => 5,
            'comment' => 'This product is amazing!',
        ];

        $this->actingAs($this->customer)
            ->post(route('customer.reviews.store', $this->product), $reviewData)
            ->assertRedirect(route('customer.products.show', $this->product));

        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
            'rating' => 5,
        ]);
    }

    public function test_store_review_requires_a_rating(): void
    {
        $this->actingAs($this->customer)
            ->post(route('customer.reviews.store', $this->product), ['comment' => 'No rating given'])
            ->assertSessionHasErrors('rating');
    }

    // Update Tests
    public function test_customer_can_update_their_own_review(): void
    {
        $review = Review::factory()->create([
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
        ]);

        $updateData = [
            'rating' => 1,
            'comment' => 'Actually, I changed my mind.',
        ];

        $this->actingAs($this->customer)
            ->put(route('customer.reviews.update', $review), $updateData)
            ->assertRedirect(route('customer.products.show', $this->product));

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'comment' => 'Actually, I changed my mind.',
        ]);
    }

    public function test_customer_cannot_update_another_users_review(): void
    {
        $review = Review::factory()->create([
            'user_id' => $this->anotherCustomer->id,
            'product_id' => $this->product->id,
        ]);

        $this->actingAs($this->customer)
            ->put(route('customer.reviews.update', $review), ['rating' => 2])
            ->assertForbidden(); // Blocked by StoreReviewRequest authorize method
    }

    // Destroy Tests
    public function test_customer_can_delete_their_own_review(): void
    {
        $review = Review::factory()->create([
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
        ]);

        $this->actingAs($this->customer)
            ->delete(route('customer.reviews.destroy', $review))
            ->assertRedirect(route('customer.products.show', $this->product));

        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    public function test_customer_cannot_delete_another_users_review(): void
    {
        $review = Review::factory()->create([
            'user_id' => $this->anotherCustomer->id,
            'product_id' => $this->product->id,
        ]);

        $this->actingAs($this->customer)
            ->delete(route('customer.reviews.destroy', $review))
            ->assertForbidden(); // Blocked by authorize() method in controller
    }
}
