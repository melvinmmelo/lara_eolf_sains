<?php

namespace Tests\Feature;

use App\Models\Branches;
use App\Models\Inbound;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalesByProductTypeReportTest extends TestCase
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

        // sequence_no is deliberately the inverse of alphabetical order, so the
        // ordering assertion can't pass by accident.
        ProductType::create(['code' => 'TUBE', 'name' => 'Tube Ice', 'volume' => '1', 'sequence_no' => 1]);
        ProductType::create(['code' => 'BLOCK', 'name' => 'Block Ice', 'volume' => '1', 'sequence_no' => 2]);
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
            'degic_no' => 'D-'.$orderNo,
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

    private function line(string $ptypeCode, float $qty, float $price, int $order = 1): array
    {
        return [
            'ptype_code' => $ptypeCode,
            'code' => $ptypeCode.'_X',
            'quantity' => $qty,
            'price' => $price,
            'order' => (string) $order,
        ];
    }

    public function test_it_groups_quantity_and_amount_by_product_type(): void
    {
        $this->makeInbound([$this->line('TUBE', 10, 25.00), $this->line('BLOCK', 2, 100.00, 2)]);
        $this->makeInbound([$this->line('TUBE', 5, 25.00)]);

        $response = $this->actingAs($this->admin)->get(route('report.sales-by-product-type'));

        $response->assertOk();
        $rows = collect($response->viewData('rows'))->keyBy('ptype_code');

        $this->assertEquals(15, $rows['TUBE']['quantity']);
        $this->assertEquals(375.00, $rows['TUBE']['amount']);   // 15 x 25
        $this->assertEquals(2, $rows['BLOCK']['quantity']);
        $this->assertEquals(200.00, $rows['BLOCK']['amount']);  // 2 x 100
    }

    /**
     * ~285 production rows store `products` as a JSON object ({"0":{...}})
     * rather than an array. Those orders must still be counted.
     */
    public function test_it_counts_orders_whose_products_json_is_object_encoded(): void
    {
        $this->makeInbound('{"0":'.json_encode($this->line('TUBE', 7, 10.00)).'}');

        $response = $this->actingAs($this->admin)->get(route('report.sales-by-product-type'));

        $rows = collect($response->viewData('rows'))->keyBy('ptype_code');
        $this->assertEquals(7, $rows['TUBE']['quantity'], 'object-encoded products JSON was skipped');
        $this->assertEquals(70.00, $rows['TUBE']['amount']);
    }

    public function test_it_orders_rows_by_sequence_no_not_alphabetically(): void
    {
        $this->makeInbound([$this->line('BLOCK', 1, 1.00), $this->line('TUBE', 1, 1.00, 2)]);

        $response = $this->actingAs($this->admin)->get(route('report.sales-by-product-type'));

        $codes = collect($response->viewData('rows'))->pluck('ptype_code')->all();
        // Alphabetical would be BLOCK, TUBE; sequence_no says TUBE (1) first.
        $this->assertSame(['TUBE', 'BLOCK'], $codes);
    }

    public function test_it_excludes_cancelled_deleted_and_foc_orders(): void
    {
        $this->makeInbound([$this->line('TUBE', 10, 10.00)]);
        $this->makeInbound([$this->line('TUBE', 99, 10.00)], ['status' => 'Cancelled']);
        $this->makeInbound([$this->line('TUBE', 99, 10.00)], ['status' => 'Deleted']);
        $this->makeInbound([$this->line('TUBE', 99, 10.00)], ['is_foc' => 1]);

        $response = $this->actingAs($this->admin)->get(route('report.sales-by-product-type'));

        $rows = collect($response->viewData('rows'))->keyBy('ptype_code');
        $this->assertEquals(10, $rows['TUBE']['quantity']);
    }

    public function test_it_excludes_other_branches(): void
    {
        $this->makeInbound([$this->line('TUBE', 10, 10.00)]);
        $this->makeInbound([$this->line('TUBE', 99, 10.00)], ['branch_code' => 'EFTO-TAR']);

        $response = $this->actingAs($this->admin)->get(route('report.sales-by-product-type'));

        $rows = collect($response->viewData('rows'))->keyBy('ptype_code');
        $this->assertEquals(10, $rows['TUBE']['quantity']);
    }

    public function test_it_honours_a_custom_date_range(): void
    {
        $this->makeInbound([$this->line('TUBE', 4, 10.00)], ['order_date' => '2026-01-15 09:00:00']);
        $this->makeInbound([$this->line('TUBE', 99, 10.00)], ['order_date' => '2026-03-20 09:00:00']);

        $response = $this->actingAs($this->admin)->get(route('report.sales-by-product-type', [
            'report_type' => 'custom',
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-31',
        ]));

        $rows = collect($response->viewData('rows'))->keyBy('ptype_code');
        $this->assertEquals(4, $rows['TUBE']['quantity']);
    }

    public function test_totals_row_equals_the_sum_of_the_per_type_rows(): void
    {
        $this->makeInbound([$this->line('TUBE', 10, 25.00), $this->line('BLOCK', 2, 100.00, 2)]);

        $response = $this->actingAs($this->admin)->get(route('report.sales-by-product-type'));

        $rows = collect($response->viewData('rows'));
        $totals = $response->viewData('totals');

        $this->assertEquals($rows->sum('quantity'), $totals['quantity']);
        $this->assertEquals($rows->sum('amount'), $totals['amount']);
        $this->assertEquals(450.00, $totals['amount']);
    }

    /**
     * Mutation-tested: removing ->middleware('can:admin') from the route makes
     * this assertion fail (200 instead of 403), so it genuinely proves the gate.
     */
    public function test_non_admin_is_denied(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('report.sales-by-product-type'))
            ->assertForbidden();
    }

    public function test_export_returns_an_xlsx_for_the_same_filter(): void
    {
        $this->makeInbound([$this->line('TUBE', 10, 25.00)]);

        $response = $this->actingAs($this->admin)->get(route('report.sales-by-product-type.export'));

        $response->assertOk();
        $this->assertSame(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('content-type')
        );
        $this->assertStringContainsString('sales_by_product_type_', $response->headers->get('content-disposition'));
    }

    /**
     * getSalesQuery() passes start_date/end_date to Carbon::parse(), which
     * throws on garbage — this must be a validation error, not a 500.
     */
    public function test_it_rejects_an_invalid_custom_date_instead_of_erroring(): void
    {
        $response = $this->actingAs($this->admin)->get(route('report.sales-by-product-type', [
            'report_type' => 'custom',
            'start_date' => 'not-a-date',
            'end_date' => 'also-not-a-date',
        ]));

        $response->assertRedirect();
        $response->assertSessionHasErrors(['start_date', 'end_date']);
    }

    public function test_it_rejects_an_end_date_before_the_start_date(): void
    {
        $this->actingAs($this->admin)
            ->get(route('report.sales-by-product-type', [
                'report_type' => 'custom',
                'start_date' => '2026-06-30',
                'end_date' => '2026-06-01',
            ]))
            ->assertSessionHasErrors('end_date');
    }

    /**
     * This report aggregates order lines only, so it must not pay for
     * getSalesQuery()'s customer/store eager loads.
     */
    public function test_it_does_not_eager_load_unused_relations(): void
    {
        $this->makeInbound([$this->line('TUBE', 1, 1.00)]);

        \DB::enableQueryLog();
        $this->actingAs($this->admin)->get(route('report.sales-by-product-type'))->assertOk();
        $queries = collect(\DB::getQueryLog())->pluck('query');
        \DB::disableQueryLog();

        $this->assertTrue(
            $queries->filter(fn ($q) => str_contains($q, 'from `customers`'))->isEmpty(),
            'customers were eager-loaded but this report never uses them'
        );
        $this->assertTrue(
            $queries->filter(fn ($q) => str_contains($q, 'from `store_infos`'))->isEmpty(),
            'stores were eager-loaded but this report never uses them'
        );
    }

    public function test_non_admin_is_denied_the_export(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('report.sales-by-product-type.export'))
            ->assertForbidden();
    }
}
