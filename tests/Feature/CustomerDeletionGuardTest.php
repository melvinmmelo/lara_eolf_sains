<?php

namespace Tests\Feature;

use App\Models\Branches;
use App\Models\Customers;
use App\Models\Equipment;
use App\Models\Inbound;
use App\Models\NewBadOrder;
use App\Models\StoreInfo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Regression cover for the 2026-06-17 incident: deleting a customer's last
 * store silently hard-deleted the CUSTOMER, orphaning 45 paid orders and
 * 500-ing /bad-orders for six weeks. See docs/STATE.md.
 */
class CustomerDeletionGuardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected string $branchCode = 'EFTO-CAG';

    protected function setUp(): void
    {
        parent::setUp();

        // The global view composer in AppServiceProvider resolves the session
        // branch, so every rendered/redirected page needs this row.
        Branches::create([
            'code' => $this->branchCode,
            'name' => 'EOLF Food Trading OPC - Cagayan Valley',
            'address' => 'Test Address',
            'office_no' => '000',
        ]);

        Role::findOrCreate('admin', 'web');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        session(['branch_code' => $this->branchCode]);
    }

    private function makeCustomer(array $overrides = []): Customers
    {
        return Customers::create(array_merge([
            'distributor' => 'n/a',
            'branch_code' => $this->branchCode,
            'lastname' => 'Batang',
            'firstname' => 'Mary Grace',
            'companyname' => 'LAROSE STORE',
            'status' => 'Active',
        ], $overrides));
    }

    private function makeStore(Customers $customer, string $name = 'Main Store'): StoreInfo
    {
        return StoreInfo::create([
            'customer_id' => $customer->id,
            'storename' => $name,
        ]);
    }

    private function makeInbound(Customers $customer): Inbound
    {
        static $orderNo = 0;
        $orderNo++;

        // Unguarded: several NOT NULL columns are absent from Inbound::$fillable.
        return Inbound::unguarded(fn () => Inbound::create([
            'branch_code' => $this->branchCode,
            'customer_id' => $customer->id,
            'customer_name' => $customer->fullName,
            'degic_no' => 'D-'.$orderNo,
            'delivery_person' => 'Someone',
            'delivery_person_id' => 1,
            'driver_id' => '1',
            'driver_name' => 'Driver',
            'equipment_id' => '1',
            'order_date' => now()->format('Y-m-d H:i:s'),
            'order_no' => (string) $orderNo,
            'pricelevel_id' => 1,
            'status' => 'Paid',
            'store_id' => 1,
            'store_name' => 'Test Store',
            'user_id' => $this->admin->id,
            'vehicle_id' => '1',
            'vehicle_no' => 'ABC-123',
            'products' => json_encode([]),
        ]));
    }

    private function makeBadOrder(Customers $customer): NewBadOrder
    {
        return NewBadOrder::create([
            'branch_code' => $this->branchCode,
            'customer_id' => $customer->id,
            'session_bo_id' => 'BO_00001',
            'degic_code' => '9697.2022',
            'bo_percentage' => 10,
            'is_active' => 1,
        ]);
    }

    private function deleteStore(Customers $customer, StoreInfo $store, array $payload = [])
    {
        return $this->actingAs($this->admin)->delete(
            route('customer.store.destroy', ['customer' => $customer->id, 'store' => $store->id]),
            $payload
        );
    }

    // ---------------------------------------------------------------------
    // Customers are never deleted (Melvin, 2026-07-30)
    // ---------------------------------------------------------------------

    public function test_there_is_no_customer_delete_route(): void
    {
        $this->assertFalse(
            \Illuminate\Support\Facades\Route::has('customer.destroy'),
            'Customers must not be deletable — retire them with stop-selling instead.'
        );
    }

    public function test_deleting_the_last_store_keeps_a_customer_with_no_history_at_all(): void
    {
        // The case that used to delete the customer even under the reference
        // guard. Nothing deletes a customer now, history or not.
        $customer = $this->makeCustomer();
        $store = $this->makeStore($customer);

        $this->deleteStore($customer, $store);

        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
        $this->assertDatabaseMissing('storeinfo', ['id' => $store->id]);
    }

    public function test_deleting_the_last_store_does_not_delete_a_customer_with_orders(): void
    {
        $customer = $this->makeCustomer();
        $store = $this->makeStore($customer);
        $this->makeInbound($customer);

        $this->deleteStore($customer, $store);

        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
        $this->assertDatabaseMissing('storeinfo', ['id' => $store->id]);
    }

    public function test_deleting_the_last_store_does_not_delete_a_customer_with_bad_orders(): void
    {
        $customer = $this->makeCustomer();
        $store = $this->makeStore($customer);
        $this->makeBadOrder($customer);

        $this->deleteStore($customer, $store);

        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
        $this->assertDatabaseMissing('storeinfo', ['id' => $store->id]);
    }

    public function test_it_tells_the_operator_the_customer_was_kept(): void
    {
        $customer = $this->makeCustomer();
        $store = $this->makeStore($customer);
        $this->makeInbound($customer);

        $response = $this->deleteStore($customer, $store);

        $response->assertSessionHas('success');
        $message = session('success');

        // The old message said "possibly customer deleted", which is how a
        // destructive cascade went unnoticed for six weeks.
        $this->assertStringNotContainsString('possibly', strtolower($message));
        $this->assertStringContainsString('kept', strtolower($message));
    }

    public function test_a_customer_with_remaining_stores_is_never_deleted(): void
    {
        $customer = $this->makeCustomer();
        $first = $this->makeStore($customer, 'First');
        $this->makeStore($customer, 'Second');

        $this->deleteStore($customer, $first);

        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
        $this->assertDatabaseMissing('storeinfo', ['id' => $first->id]);
    }

    public function test_it_still_releases_the_equipment_back_to_available(): void
    {
        $customer = $this->makeCustomer();
        $store = $this->makeStore($customer);
        $this->makeInbound($customer);

        $equipment = Equipment::create([
            'ownership' => 'company',
            'type' => 'freezer',
            'brand' => 'Test Brand',
            'status' => 'deployed',
            'branch_code' => $this->branchCode,
        ]);

        $this->deleteStore($customer, $store, ['equipment_ids' => [$equipment->id]]);

        $this->assertSame('available', $equipment->fresh()->status);
    }

    public function test_no_view_renders_a_customer_delete_form(): void
    {
        // Three views used to POST a DELETE to customer.destroy. With the route
        // gone, any leftover route() call would throw at render time.
        $views = [
            resource_path('views/customers.blade.php'),
            resource_path('views/edit_customer.blade.php'),
            resource_path('views/create-customer.blade.php'),
        ];

        foreach ($views as $view) {
            $this->assertStringNotContainsString(
                'customer.destroy',
                file_get_contents($view),
                basename($view).' still references the removed customer.destroy route.'
            );
        }
    }
}
