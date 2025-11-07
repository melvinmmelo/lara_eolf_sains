<?php

namespace Tests\Feature;

use App\Models\Customers;
use App\Models\Drivers;
use App\Models\Equipment;
use App\Models\EquipmentStore;
use App\Models\Inbound;
use App\Models\ItemMasterData;
use App\Models\NewInboundProduct;
use App\Models\pricelevels;
use App\Models\prices;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\StoreInfo;
use App\Models\User;
use App\Models\Vehicles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class InboundControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected string $branchCode = 'EFTO-CAG';

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user with required permissions
        $this->user = User::factory()->create();
        session(['branch_code' => $this->branchCode]);
    }

    /**
     * Test: Database transaction rollback on store() failure
     * Batch 1 - Critical Bug Fix
     */
    public function test_store_rolls_back_transaction_on_insufficient_stock(): void
    {
        $this->actingAs($this->user);

        // Setup test data
        $testData = $this->createOrderTestData();

        // Create product with insufficient stock
        $product = Product::factory()->create(['code' => 'TEST_001']);
        ItemMasterData::factory()->create([
            'branch_code' => $this->branchCode,
            'product_code' => 'TEST_001',
            'stocks' => 5,
            'reserved' => 0
        ]);

        // Create new inbound product with quantity exceeding stock
        NewInboundProduct::factory()->create([
            'inbound_id' => 0,
            'branch_code' => $this->branchCode,
            'code' => 'TEST_001',
            'quantity' => 10, // More than available
        ]);

        $initialInboundCount = Inbound::count();
        $initialItemData = ItemMasterData::branch($this->branchCode)
            ->productCode('TEST_001')
            ->first();
        $initialReserved = $initialItemData->reserved;

        // Attempt to create order
        $response = $this->post(route('order.store'), array_merge($testData, [
            'form_submit_token' => bin2hex(random_bytes(16))
        ]));

        // Assert transaction rolled back
        $this->assertEquals($initialInboundCount, Inbound::count(), 'Inbound should not be created');

        $itemData = ItemMasterData::branch($this->branchCode)
            ->productCode('TEST_001')
            ->first();

        $this->assertEquals($initialReserved, $itemData->reserved, 'Reserved stock should not change');

        $response->assertSessionHasErrors();
    }

    /**
     * Test: Stock validation happens BEFORE save
     * Batch 1 - Critical Bug Fix
     */
    public function test_ajax_inbound_list_validates_stock_before_save(): void
    {
        $this->actingAs($this->user);

        // Setup
        $product = Product::factory()->create(['code' => 'TEST_002']);
        $priceLevel = pricelevels::factory()->create();
        prices::factory()->create([
            'p_code' => 'TEST_002',
            'pricelevel_id' => $priceLevel->id,
            'p_price' => 100,
            'p_unit' => 'pcs'
        ]);

        ItemMasterData::factory()->create([
            'branch_code' => $this->branchCode,
            'product_code' => 'TEST_002',
            'stocks' => 5,
            'reserved' => 0
        ]);

        session(['pricelevelId' => $priceLevel->id]);

        $initialProductCount = NewInboundProduct::count();

        // Try to add more than available
        $response = $this->get(route('ajax.inbound.list', [
            'code' => 'TEST_002',
            'qty' => 10,
            'pid' => $priceLevel->id,
            'inboundId' => 0
        ]));

        // Assert product was NOT saved
        $this->assertEquals($initialProductCount, NewInboundProduct::count());
        $response->assertJson(['error' => 'Insufficient stocks.']);
    }

    /**
     * Test: Pessimistic locking prevents race conditions
     * Batch 1 - Critical Bug Fix
     */
    public function test_store_uses_pessimistic_locking(): void
    {
        $this->actingAs($this->user);

        $testData = $this->createOrderTestData();
        $product = Product::factory()->create(['code' => 'TEST_003']);

        ItemMasterData::factory()->create([
            'branch_code' => $this->branchCode,
            'product_code' => 'TEST_003',
            'stocks' => 100,
            'reserved' => 0
        ]);

        NewInboundProduct::factory()->create([
            'inbound_id' => 0,
            'branch_code' => $this->branchCode,
            'code' => 'TEST_003',
            'quantity' => 10,
        ]);

        // Monitor queries to verify lockForUpdate is called
        DB::enableQueryLog();

        $response = $this->post(route('order.store'), array_merge($testData, [
            'form_submit_token' => bin2hex(random_bytes(16))
        ]));

        $queries = DB::getQueryLog();

        // Check that at least one query uses FOR UPDATE
        $hasLock = collect($queries)->contains(function ($query) {
            return str_contains(strtoupper($query['query']), 'FOR UPDATE');
        });

        $this->assertTrue($hasLock, 'Store method should use pessimistic locking (FOR UPDATE)');

        DB::disableQueryLog();
    }

    /**
     * Test: ResetInventory uses get() instead of first()
     * Batch 1 - Critical Bug Fix
     */
    public function test_reset_inventory_handles_multiple_delivery_receipts(): void
    {
        // This test would require DeliveryPurchaseReceipt setup
        // Marking as reminder that the bug is fixed (line 42: changed first() to get())
        $this->markTestIncomplete('DeliveryPurchaseReceipt factory needs to be created');
    }

    /**
     * Test: updateInbound() validates stock increases
     * Batch 1 - Critical Bug Fix
     */
    public function test_update_inbound_validates_stock_increase(): void
    {
        $this->actingAs($this->user);

        // Create existing order
        $inbound = $this->createExistingOrder();

        // Create NewInboundProduct with increased quantity
        $product = json_decode($inbound->products, true)[0];

        NewInboundProduct::factory()->create([
            'inbound_id' => $inbound->id,
            'branch_code' => $this->branchCode,
            'code' => $product['code'],
            'old_quantity' => $product['quantity'],
            'quantity' => $product['quantity'] + 100, // Try to add 100 more
        ]);

        // Item only has 5 available
        $itemData = ItemMasterData::branch($this->branchCode)
            ->productCode($product['code'])
            ->first();
        $itemData->stocks = 50;
        $itemData->reserved = 45;
        $itemData->save();

        $testData = $this->createOrderTestData();

        $response = $this->post(route('order.update.inbound'), array_merge($testData, [
            'inbound_id' => $inbound->id
        ]));

        $response->assertSessionHasErrors();
    }

    /**
     * Test: destroy() properly restores inventory with transaction
     * Batch 1 - Critical Bug Fix
     */
    public function test_destroy_restores_inventory_atomically(): void
    {
        $this->actingAs($this->user);

        $inbound = $this->createExistingOrder();
        $product = json_decode($inbound->products, true)[0];

        $itemBefore = ItemMasterData::branch($this->branchCode)
            ->productCode($product['code'])
            ->first();

        $reservedBefore = $itemBefore->reserved;

        $response = $this->post(route('order.destroy'), [
            'inbound_id' => $inbound->id,
            'confirm_delete' => 'Delete',
            'remarks' => 'Cancelled',
            'remarks_details' => 'Test cancellation'
        ]);

        $itemAfter = ItemMasterData::branch($this->branchCode)
            ->productCode($product['code'])
            ->first();

        // Reserved should be reduced by order quantity
        $this->assertEquals(
            $reservedBefore - $product['quantity'],
            $itemAfter->reserved,
            'Reserved stock should be released'
        );

        $response->assertRedirect(route('order.index'));
        $response->assertSessionHas('success');
    }

    /**
     * Test: Logic error fix - $products !== null
     * Batch 1 - Bug Fix
     */
    public function test_update_method_handles_null_products_correctly(): void
    {
        $this->actingAs($this->user);

        $inbound = $this->createExistingOrder();
        $product = json_decode($inbound->products, true)[0];

        NewInboundProduct::factory()->create([
            'inbound_id' => $inbound->id,
            'branch_code' => $this->branchCode,
            'code' => $product['code'],
            'quantity' => 5,
        ]);

        // This should not throw an error with the fixed logic
        $response = $this->post(route('order.update'), [
            'code' => $product['code'],
            'action' => 'add',
            'inboundId' => $inbound->id
        ]);

        $response->assertOk();
    }

    /**
     * Helper: Create test data for order creation
     */
    private function createOrderTestData(): array
    {
        $customer = Customers::factory()->create();
        $driver = Drivers::factory()->create(['designation' => 'Driver']);
        $deliveryPerson = Drivers::factory()->create(['designation' => 'Salesman']);
        $vehicle = Vehicles::factory()->create();
        $equipment = Equipment::factory()->create(['branch_code' => $this->branchCode]);
        $store = StoreInfo::factory()->create();
        $priceLevel = pricelevels::factory()->create();

        $equipStore = EquipmentStore::factory()->create([
            'equipment_id' => $equipment->id,
            'store_id' => $store->id,
            'customer_id' => $customer->id
        ]);

        return [
            'pricelevel_id' => $priceLevel->id,
            'customer_id' => $customer->id,
            'equipment_id' => $equipStore->id,
            'driver_id' => $driver->id,
            'delivery_person_id' => $deliveryPerson->id,
            'vehicle_id' => $vehicle->id,
            'order_date' => now()->format('Y-m-d'),
        ];
    }

    /**
     * Helper: Create an existing order for update/delete tests
     */
    private function createExistingOrder(): Inbound
    {
        $product = Product::factory()->create(['code' => 'EXIST_001']);
        $productType = ProductType::factory()->create();

        ItemMasterData::factory()->create([
            'branch_code' => $this->branchCode,
            'product_code' => 'EXIST_001',
            'stocks' => 100,
            'reserved' => 10
        ]);

        $testData = $this->createOrderTestData();

        $inbound = Inbound::create([
            'user_id' => $this->user->id,
            'order_no' => 1,
            'branch_code' => $this->branchCode,
            'equipment_id' => Equipment::first()->id,
            'store_id' => StoreInfo::first()->id,
            'driver_id' => $testData['driver_id'],
            'delivery_person_id' => $testData['delivery_person_id'],
            'vehicle_id' => $testData['vehicle_id'],
            'products' => json_encode([
                [
                    'code' => 'EXIST_001',
                    'quantity' => 10,
                    'price' => 100,
                    'ptype_code' => $productType->code,
                    'order' => 1
                ]
            ]),
            'pricelevel_id' => $testData['pricelevel_id'],
            'customer_id' => $testData['customer_id'],
            'degic_no' => 'TEST001',
            'customer_name' => 'Test Customer',
            'store_name' => 'Test Store',
            'driver_name' => 'Test Driver',
            'delivery_person' => 'Test Delivery',
            'vehicle_no' => 'ABC123',
            'status' => 'Completed',
            'order_date' => now(),
        ]);

        return $inbound;
    }
}
