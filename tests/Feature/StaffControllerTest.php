<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffControllerTest extends TestCase
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

    // Auth Tests
    public function test_non_admins_are_forbidden(): void
    {
        $this->actingAs($this->staff)->get(route('admin.staffs.index'))
            ->assertRedirect(route('staff.dashboard'));
        $this->actingAs($this->customer)->get(route('admin.staffs.index'))
            ->assertRedirect(route('customer.home'));
    }

    // Index Tests
    public function test_admin_can_view_staff_list(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.staffs.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Staff/StaffsRecord')
                ->has('staffs.data', 1)
                ->where('staffs.data.0.role', 'staff')
            );
    }

    // Store Tests
    public function test_admin_can_create_a_staff_user(): void
    {
        $staffData = [
            'name' => 'New Staff',
            'email' => 'staff@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'type' => 'staff',
        ];

        $this->actingAs($this->admin)
            ->post(route('admin.staffs.store'), $staffData)
            ->assertRedirect(route('admin.staffs.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'staff@example.com',
            'role' => 'staff',
        ]);
    }

    public function test_creating_user_without_staff_type_creates_customer(): void
    {
        $customerData = [
            'name' => 'New Customer',
            'email' => 'customer@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->actingAs($this->admin)
            ->post(route('admin.staffs.store'), $customerData)
            ->assertRedirect(route('admin.staffs.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'customer@example.com',
            'role' => 'customer',
        ]);
    }

    public function test_store_staff_requires_password(): void
    {
        $staffData = ['name' => 'Test', 'email' => 'test@test.com', 'type' => 'staff'];
        $this->actingAs($this->admin)
            ->post(route('admin.staffs.store'), $staffData)
            ->assertSessionHasErrors('password');
    }

    // Update Tests
    public function test_admin_can_update_staff_user(): void
    {
        $updateData = [
            'name' => 'Updated Staff Name',
            'email' => $this->staff->email, // Email must be unique, so keep it the same
        ];

        $this->actingAs($this->admin)
            ->put(route('admin.staffs.update', $this->staff), $updateData)
            ->assertRedirect(route('admin.staffs.index'));

        $this->assertDatabaseHas('users', ['id' => $this->staff->id, 'name' => 'Updated Staff Name']);
    }

    // Destroy Tests
    public function test_admin_can_delete_staff_user(): void
    {
        $staffToDelete = User::factory()->create(['role' => UserRole::Staff]);

        $this->actingAs($this->admin)
            ->delete(route('admin.staffs.destroy', $staffToDelete))
            ->assertRedirect(route('admin.staffs.index'));

        $this->assertDatabaseMissing('users', ['id' => $staffToDelete->id]);
    }
}
