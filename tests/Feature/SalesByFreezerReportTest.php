<?php

namespace Tests\Feature;

use App\Models\Branches;
use App\Models\Inbound;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalesByFreezerReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected string $branchCode = 'EFTO-CAG';

    protected function setUp(): void
    {
        parent::setUp();

        // The global view composer in AppServiceProvider resolves the session
        // branch and reads ->name on it unguarded, so every rendered page needs
        // this row to exist.
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

    /**
     * Build an inbound with every NOT NULL column satisfied.
     *
     * @param  array<int|string, array<string, mixed>>|string  $products
     */
    private function makeInbound(array|string $products, array $overrides = []): Inbound
    {
        static $orderNo = 0;
        $orderNo++;

        // Unguarded: several NOT NULL columns (delivery_person, vehicle_no, ...)
        // are absent from Inbound::$fillable, so mass assignment would drop them.
        return Inbound::unguarded(fn () => Inbound::create(array_merge([
            'branch_code' => $this->branchCode,
            'customer_id' => 1,
            'customer_name' => 'Test Customer',
            'degic_no' => 'DEGIC-1',
            'delivery_person' => 'Someone',
            'delivery_person_id' => 1,
            'driver_id' => '1',
            'driver_name' => 'Driver',
            'equipment_id' => '1',
            'order_date' => now()->format('Y-m-d H:i:s'),
            'order_no' => (string) $orderNo,
            'pricelevel_id' => 1,
            'status' => 'Completed',
            'store_id' => 1,
            'store_name' => 'Test Store',
            'user_id' => $this->admin->id,
            'vehicle_id' => '1',
            'vehicle_no' => 'ABC-123',
            'products' => is_string($products) ? $products : json_encode($products),
        ], $overrides)));
    }

    private function line(float $qty, float $price): array
    {
        return [
            'ptype_code' => 'TUBE',
            'code' => 'TUBE_X',
            'quantity' => $qty,
            'price' => $price,
            'order' => '1',
        ];
    }

    public function test_it_buckets_amounts_per_freezer_per_month(): void
    {
        $year = now()->year;

        $this->makeInbound([$this->line(10, 25.00)], ['order_date' => $year.'-01-15 09:00:00']);
        $this->makeInbound([$this->line(5, 25.00)], ['order_date' => $year.'-01-20 09:00:00']);
        $this->makeInbound([$this->line(2, 100.00)], ['order_date' => $year.'-03-05 09:00:00']);
        $this->makeInbound([$this->line(1, 50.00)], ['order_date' => $year.'-03-05 09:00:00', 'degic_no' => 'DEGIC-2']);

        $response = $this->actingAs($this->admin)->get(route('report.sales-by-freezer'));

        $response->assertOk();
        $rows = collect($response->viewData('rows'))->keyBy('degic_no');

        $this->assertEquals(375.00, $rows['DEGIC-1']['months'][1]); // 10x25 + 5x25
        $this->assertEquals(200.00, $rows['DEGIC-1']['months'][3]);
        $this->assertEquals(575.00, $rows['DEGIC-1']['total']);
        $this->assertEquals(50.00, $rows['DEGIC-2']['months'][3]);
        $this->assertEquals(50.00, $rows['DEGIC-2']['total']);
    }

    public function test_orders_without_a_degic_code_get_their_own_na_row(): void
    {
        $this->makeInbound([$this->line(1, 10.00)], ['degic_no' => null]);
        $this->makeInbound([$this->line(1, 20.00)]);

        $response = $this->actingAs($this->admin)->get(route('report.sales-by-freezer'));

        $rows = collect($response->viewData('rows'))->keyBy('degic_no');
        $this->assertEquals(10.00, $rows['N/A']['total']);
        $this->assertEquals(20.00, $rows['DEGIC-1']['total']);
    }

    /**
     * ~285 production rows store `products` as a JSON object ({"0":{...}})
     * rather than an array. Those orders must still be counted.
     */
    public function test_it_counts_orders_whose_products_json_is_object_encoded(): void
    {
        $this->makeInbound('{"0":'.json_encode($this->line(7, 10.00)).'}');

        $response = $this->actingAs($this->admin)->get(route('report.sales-by-freezer'));

        $rows = collect($response->viewData('rows'))->keyBy('degic_no');
        $this->assertEquals(70.00, $rows['DEGIC-1']['total'], 'object-encoded products JSON was skipped');
    }

    public function test_it_honours_the_year_filter(): void
    {
        $this->makeInbound([$this->line(4, 10.00)], ['order_date' => '2024-06-15 09:00:00']);
        $this->makeInbound([$this->line(99, 10.00)], ['order_date' => '2025-06-15 09:00:00']);

        $response = $this->actingAs($this->admin)->get(route('report.sales-by-freezer', ['year' => 2024]));

        $rows = collect($response->viewData('rows'));
        $this->assertCount(1, $rows);
        $this->assertEquals(40.00, $rows->first()['total']);
        $this->assertSame(2024, $response->viewData('year'));
    }

    public function test_it_excludes_cancelled_deleted_and_foc_orders(): void
    {
        $this->makeInbound([$this->line(10, 10.00)]);
        $this->makeInbound([$this->line(99, 10.00)], ['status' => 'Cancelled']);
        $this->makeInbound([$this->line(99, 10.00)], ['status' => 'Deleted']);
        $this->makeInbound([$this->line(99, 10.00)], ['is_foc' => 1]);

        $response = $this->actingAs($this->admin)->get(route('report.sales-by-freezer'));

        $rows = collect($response->viewData('rows'))->keyBy('degic_no');
        $this->assertEquals(100.00, $rows['DEGIC-1']['total']);
    }

    public function test_it_excludes_other_branches(): void
    {
        $this->makeInbound([$this->line(10, 10.00)]);
        $this->makeInbound([$this->line(99, 10.00)], ['branch_code' => 'EFTO-TAR']);

        $response = $this->actingAs($this->admin)->get(route('report.sales-by-freezer'));

        $rows = collect($response->viewData('rows'))->keyBy('degic_no');
        $this->assertEquals(100.00, $rows['DEGIC-1']['total']);
    }

    public function test_the_same_degic_code_under_two_customers_is_two_rows(): void
    {
        $this->makeInbound([$this->line(1, 10.00)], ['customer_id' => 1, 'customer_name' => 'Alpha']);
        $this->makeInbound([$this->line(1, 20.00)], ['customer_id' => 2, 'customer_name' => 'Beta']);

        $response = $this->actingAs($this->admin)->get(route('report.sales-by-freezer'));

        $rows = collect($response->viewData('rows'));
        $this->assertCount(2, $rows);
        // Sorted by customer name.
        $this->assertSame(['Alpha', 'Beta'], $rows->pluck('customer_name')->all());
    }

    public function test_column_totals_equal_the_sum_of_the_rows(): void
    {
        $year = now()->year;
        $this->makeInbound([$this->line(10, 25.00)], ['order_date' => $year.'-02-01 09:00:00']);
        $this->makeInbound([$this->line(2, 100.00)], ['order_date' => $year.'-02-01 09:00:00', 'degic_no' => 'DEGIC-2']);

        $response = $this->actingAs($this->admin)->get(route('report.sales-by-freezer'));

        $totals = $response->viewData('totals');
        $this->assertEquals(450.00, $totals['months'][2]);
        $this->assertEquals(450.00, $totals['grand']);
    }

    public function test_it_rejects_an_invalid_year_instead_of_erroring(): void
    {
        $this->actingAs($this->admin)
            ->get(route('report.sales-by-freezer', ['year' => 'not-a-year']))
            ->assertSessionHasErrors('year');
    }

    /**
     * Mutation-tested pattern from the product-type report: without
     * ->middleware('can:admin') this returns 200 instead of 403.
     */
    public function test_non_admin_is_denied(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('report.sales-by-freezer'))
            ->assertForbidden();
    }

    public function test_non_admin_is_denied_the_export(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('report.sales-by-freezer.export'))
            ->assertForbidden();
    }

    public function test_export_returns_an_xlsx_for_the_same_filter(): void
    {
        $this->makeInbound([$this->line(10, 25.00)]);

        $response = $this->actingAs($this->admin)->get(route('report.sales-by-freezer.export'));

        $response->assertOk();
        $this->assertSame(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('content-type')
        );
        $this->assertStringContainsString('sales_by_freezer_', $response->headers->get('content-disposition'));
    }
}
