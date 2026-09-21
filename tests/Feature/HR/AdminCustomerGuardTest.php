<?php

namespace Tests\Feature\HR;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithAdminPermissions;
use Tests\TestCase;

class AdminCustomerGuardTest extends TestCase
{
    use InteractsWithAdminPermissions;
    use RefreshDatabase;

    public function test_admin_store_ignores_spoofed_type_and_does_not_require_customer_permission(): void
    {
        $actor = $this->createAdminWithPermissions(['hr.admins.create']);

        $this->actingAs($actor)->post(route('admin.admins.store'), [
            'name' => 'New Admin',
            'email' => 'new.admin@example.com',
            'mobile' => '01012345678',
            'password' => 'secret123',
            'type' => User::TYPE_CUSTOMER,
        ])->assertRedirect(route('admin.admins.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'new.admin@example.com',
            'type' => User::TYPE_ADMIN,
        ]);
    }

    public function test_customer_store_ignores_spoofed_admin_type(): void
    {
        $actor = $this->createAdminWithPermissions(['sales.customers.create']);

        $this->actingAs($actor)->post(route('admin.customers.store'), [
            'name' => 'New Customer',
            'email' => 'new.customer@example.com',
            'mobile' => '01087654321',
            'password' => 'secret123',
            'type' => User::TYPE_ADMIN,
        ])->assertRedirect(route('admin.customers.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'new.customer@example.com',
            'type' => User::TYPE_CUSTOMER,
        ]);
    }

    public function test_admin_update_cannot_target_a_customer(): void
    {
        $actor = $this->createAdminWithPermissions(['hr.admins.edit']);
        $customer = User::factory()->create([
            'type' => User::TYPE_CUSTOMER,
        ]);

        $this->actingAs($actor)
            ->put(route('admin.admins.update', $customer), [
                'name' => $customer->name,
                'email' => $customer->email,
                'mobile' => $customer->mobile,
            ])
            ->assertNotFound();
    }

    public function test_customer_update_cannot_target_an_admin(): void
    {
        $actor = $this->createAdminWithPermissions(['sales.customers.edit']);
        $admin = User::factory()->admin()->create();

        $this->actingAs($actor)
            ->put(route('admin.customers.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'mobile' => $admin->mobile,
            ])
            ->assertNotFound();
    }
}
