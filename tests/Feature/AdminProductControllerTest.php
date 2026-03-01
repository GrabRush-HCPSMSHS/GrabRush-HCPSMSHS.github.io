<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use App\Services\ProductImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Tests\TestCase;

class AdminProductControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $staff;
    private User $customer;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
        $this->staff = User::factory()->create(['role' => UserRole::Staff]);
        $this->customer = User::factory()->create(['role' => UserRole::Customer]);
        $this->category = Category::factory()->create();
    }

    // Index Tests
    public function test_admin_can_view_products_page(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Admin/Product/ProductsRecord'));
    }

    public function test_non_admins_cannot_view_products_page(): void
    {
        $this->actingAs($this->staff)->get(route('admin.products.index'))
            ->assertRedirect(route('staff.dashboard'));
        $this->actingAs($this->customer)->get(route('admin.products.index'))
            ->assertRedirect(route('customer.home'));
    }

    public function test_products_are_listed_correctly_with_pagination(): void
    {
        Product::factory()->count(20)->create(['category_id' => $this->category->id]);

        $this->actingAs($this->admin)
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('products', 10) // Default pagination size is 10
                ->where('products_pagination.total', 20)
            );
    }

    public function test_product_filtering_by_name_works(): void
    {
        Product::factory()->create(['name' => 'Special Laptop', 'category_id' => $this->category->id]);
        Product::factory()->create(['name' => 'Regular Keyboard', 'category_id' => $this->category->id]);

        $this->actingAs($this->admin)
            ->get(route('admin.products.index', ['filterProductName' => 'Laptop']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('products', 1)
                ->where('products.0.name', 'Special Laptop')
            );
    }

    // Show Tests
    public function test_admin_can_view_a_product(): void
    {
        $product = Product::factory()->create(['is_available' => false, 'quantity' => 0]);

        $this->actingAs($this->admin)
            ->get(route('admin.products.show', $product))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Product/ProductShow')
                ->has('product')
                ->where('product.id', $product->id)
            );
    }

    // Store Tests
    public function test_admin_can_create_a_product(): void
    {
        $productImage = ProductImage::factory()->create();
        $productData = [
            'name' => 'A New Product',
            'description' => 'Product description',
            'price' => 199.99,
            'quantity' => 50,
            'is_available' => true,
            'category_id' => $this->category->id,
            'product_image_id' => $productImage->id,
        ];

        $this->mock(ProductImageService::class, function (MockInterface $mock) use ($productImage) {
            $mock->shouldReceive('assignImage')->once()->with($productImage->id, \Mockery::any());
        });

        $this->actingAs($this->admin)
            ->post(route('admin.products.store'), $productData)
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', ['name' => 'A New Product']);
    }

    public function test_store_product_requires_a_name(): void
    {
        $productImage = ProductImage::factory()->create();
        $productData = Product::factory()->make(['name' => '', 'product_image_id' => $productImage->id])->toArray();

        $this->actingAs($this->admin)
            ->post(route('admin.products.store'), $productData)
            ->assertSessionHasErrors('name');
    }

    // Update Tests
    public function test_admin_can_update_a_product(): void
    {
        $product = Product::factory()->has(ProductImage::factory(), 'image')->create();
        $newProductImage = ProductImage::factory()->create();
        $updateData = [
            'name' => 'Updated Product Name',
            'description' => $product->description,
            'price' => $product->price,
            'quantity' => $product->quantity,
            'is_available' => (int) $product->is_available,
            'category_id' => $product->category_id,
            'product_image_id' => $newProductImage->id,
            'old_product_image_id' => $product->image->id,
        ];

        $this->mock(ProductImageService::class, function (MockInterface $mock) use ($newProductImage, $product) {
            $mock->shouldReceive('completeDelete')->once()->with(
                \Mockery::on(function ($arg) use ($product) {
                    return $arg instanceof ProductImage && $arg->id === $product->image->id;
                })
            );
            $mock->shouldReceive('assignImage')->once()->with($newProductImage->id, $product->id);
        });

        $this->actingAs($this->admin)
            ->put(route('admin.products.update', $product), $updateData)
            ->assertRedirect(route('admin.products.show', $product));

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Updated Product Name']);
    }

    // Destroy Tests
    public function test_admin_can_delete_a_product(): void
    {
        $product = Product::factory()->has(ProductImage::factory(), 'image')->create();

        $this->mock(ProductImageService::class, function (MockInterface $mock) use ($product) {
            $mock->shouldReceive('completeDelete')->once()->with(
                \Mockery::on(function ($arg) use ($product) {
                    return $arg instanceof ProductImage && $arg->id === $product->image->id;
                })
            );
        });

        $this->actingAs($this->admin)
            ->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_admin_cannot_delete_product_with_completed_order(): void
    {
        $product = Product::factory()->create();
        $order = Order::factory()->create(['status' => OrderStatus::Complete]);
        OrderItem::factory()->create(['product_id' => $product->id, 'order_id' => $order->id]);

        $this->actingAs($this->admin)
            ->delete(route('admin.products.destroy', $product))
            ->assertForbidden();

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }
}
