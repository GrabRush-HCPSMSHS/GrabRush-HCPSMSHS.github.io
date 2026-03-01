<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $staff;
    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
        $this->staff = User::factory()->create(['role' => UserRole::Staff]);
        $this->customer = User::factory()->create(['role' => UserRole::Customer]);
    }

    // Index tests
    public function test_admin_can_view_categories_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.categories.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('Admin/Category/CategoriesRecord'));
    }

    public function test_staff_cannot_view_categories_page(): void
    {
        $this->actingAs($this->staff)->get(route('admin.categories.index'))
            ->assertRedirect(route('staff.dashboard'));
    }

    public function test_customer_cannot_view_categories_page(): void
    {
        $this->actingAs($this->customer)->get(route('admin.categories.index'))
            ->assertRedirect(route('customer.home'));
    }

    public function test_categories_are_listed_correctly(): void
    {
        $categories = Category::factory()->count(3)->create();
        $expectedNames = $categories->sortBy('name')->pluck('name')->all();

        $this->actingAs($this->admin)->get(route('admin.categories.index'))
            ->assertOk()
            ->assertInertia(function ($page) use ($expectedNames) {
                $page->has('categories.data', 3);
                $actualNames = collect($page->toArray()['props']['categories']['data'])->pluck('name')->sort()->values()->all();
                $this->assertEquals($expectedNames, $actualNames);
            });
    }

    public function test_category_filtering_works(): void
    {
        Category::factory()->create(['name' => 'Test Category']);
        Category::factory()->create(['name' => 'Another Category']);

        $response = $this->actingAs($this->admin)->get(route('admin.categories.index', ['filterCategoryName' => 'Test']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('categories.data', 1)
            ->where('categories.data.0.name', 'Test Category')
        );
    }

    // Store tests
    public function test_admin_can_create_a_category(): void
    {
        $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name' => 'New Category',
        ])->assertSessionDoesntHaveErrors()->assertOk();

        $this->assertDatabaseHas('categories', ['name' => 'New Category']);
    }

    public function test_staff_cannot_create_a_category(): void
    {
        $this->actingAs($this->staff)->post(route('admin.categories.store'), [
            'name' => 'New Category',
        ])->assertRedirect(route('staff.dashboard'));

        $this->assertDatabaseMissing('categories', ['name' => 'New Category']);
    }

    public function test_customer_cannot_create_a_category(): void
    {
        $this->actingAs($this->customer)->post(route('admin.categories.store'), [
            'name' => 'New Category',
        ])->assertRedirect(route('customer.home'));

        $this->assertDatabaseMissing('categories', ['name' => 'New Category']);
    }

    public function test_store_category_validation_requires_name(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    // Update tests
    public function test_admin_can_update_a_category(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->admin)->put(route('admin.categories.update', $category), [
            'name' => 'Updated Category',
        ])->assertSessionDoesntHaveErrors()->assertOk();

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Updated Category']);
    }

    public function test_staff_cannot_update_a_category(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->staff)->put(route('admin.categories.update', $category), [
            'name' => 'Updated Category',
        ])->assertRedirect(route('staff.dashboard'));
    }

    public function test_customer_cannot_update_a_category(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->customer)->put(route('admin.categories.update', $category), [
            'name' => 'Updated Category',
        ])->assertRedirect(route('customer.home'));
    }

    public function test_update_category_validation_requires_name(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)->put(route('admin.categories.update', $category), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    // Destroy tests
    public function test_admin_can_delete_a_category(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->admin)->delete(route('admin.categories.destroy', $category))
            ->assertSessionDoesntHaveErrors()
            ->assertOk();

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_staff_cannot_delete_a_category(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->staff)->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('staff.dashboard'));

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_customer_cannot_delete_a_category(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->customer)->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('customer.home'));

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_admin_cannot_delete_category_with_completed_order_items(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $order = Order::factory()->create(['status' => OrderStatus::Complete->value]);
        OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id]);

        $this->actingAs($this->admin)->delete(route('admin.categories.destroy', $category))
            ->assertForbidden();

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}
