<?php

namespace Tests\Feature;

use App\Models\Inbound;
use App\Models\ProductType;
use App\Models\User;
use App\Services\InboundService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Covers InboundService::getTotalOfAllInboundProducts*, which back the
 * /reports/products-summary screens. These used to run one ProductType query
 * per order LINE (9,040 queries for a single month of one branch).
 */
class InboundServiceProductSummaryTest extends TestCase
{
    use RefreshDatabase;

    protected string $branchCode = 'EFTO-CAG';

    protected function setUp(): void
    {
        parent::setUp();

        // sequence_no is the inverse of alphabetical, so ordering can't pass by luck.
        ProductType::create(['code' => 'TUBE', 'name' => 'Tube Ice', 'volume' => '1', 'sequence_no' => 1]);
        ProductType::create(['code' => 'BLOCK', 'name' => 'Block Ice', 'volume' => '1', 'sequence_no' => 2]);
    }

    private function makeInbound(array|string $products, array $overrides = []): Inbound
    {
        static $orderNo = 0;
        $orderNo++;

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
            'user_id' => User::factory()->create()->id,
            'vehicle_id' => '1',
            'vehicle_no' => 'ABC-123',
            'products' => is_string($products) ? $products : json_encode($products),
        ], $overrides)));
    }

    private function line(string $ptypeCode, string $code, float $qty): array
    {
        return ['ptype_code' => $ptypeCode, 'code' => $code, 'quantity' => $qty, 'price' => 10.0, 'order' => '1'];
    }

    public function test_it_totals_quantity_per_product_code(): void
    {
        $this->makeInbound([$this->line('TUBE', 'TUBE_S', 10), $this->line('TUBE', 'TUBE_L', 3)]);
        $this->makeInbound([$this->line('TUBE', 'TUBE_S', 5)]);

        $result = collect(InboundService::getTotalOfAllInboundProducts($this->branchCode))->keyBy('code');

        $this->assertEquals(15, $result['TUBE_S']['quantity']);
        $this->assertEquals(3, $result['TUBE_L']['quantity']);
    }

    public function test_it_orders_by_sequence_no_not_alphabetically(): void
    {
        $this->makeInbound([$this->line('BLOCK', 'BLOCK_A', 1), $this->line('TUBE', 'TUBE_Z', 1)]);

        $codes = collect(InboundService::getTotalOfAllInboundProducts($this->branchCode))->pluck('code')->all();

        // Alphabetically BLOCK_A precedes TUBE_Z; sequence_no puts TUBE first.
        $this->assertSame(['TUBE_Z', 'BLOCK_A'], $codes);
    }

    /**
     * The N+1 guard. Previously this ran one ProductType query per order line;
     * the count must not grow with the number of lines.
     */
    public function test_it_does_not_query_product_types_per_line(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->makeInbound([
                $this->line('TUBE', 'TUBE_S', 1),
                $this->line('TUBE', 'TUBE_L', 1),
                $this->line('BLOCK', 'BLOCK_A', 1),
            ]);
        }

        DB::enableQueryLog();
        InboundService::getTotalOfAllInboundProducts($this->branchCode);
        $productTypeQueries = collect(DB::getQueryLog())
            ->filter(fn ($q) => str_contains($q['query'], 'from `product_types`'))
            ->count();
        DB::disableQueryLog();

        // 15 order lines, but the lookup is fetched once.
        $this->assertSame(1, $productTypeQueries, 'product_types was queried per line (N+1)');
    }

    /**
     * A line naming a product type that no longer exists used to fatal on
     * `$productType->sequence_no`. It must now sort last instead.
     */
    public function test_a_line_with_an_unknown_product_type_does_not_error(): void
    {
        $this->makeInbound([$this->line('TUBE', 'TUBE_S', 1), $this->line('GONE', 'GONE_X', 2)]);

        $result = collect(InboundService::getTotalOfAllInboundProducts($this->branchCode));

        $this->assertSame(['TUBE_S', 'GONE_X'], $result->pluck('code')->all());
        $this->assertEquals(2, $result->keyBy('code')['GONE_X']['quantity']);
    }

    public function test_it_counts_object_encoded_products_json(): void
    {
        $this->makeInbound('{"0":'.json_encode($this->line('TUBE', 'TUBE_S', 7)).'}');

        $result = collect(InboundService::getTotalOfAllInboundProducts($this->branchCode))->keyBy('code');

        $this->assertEquals(7, $result['TUBE_S']['quantity']);
    }

    public function test_v2_totals_across_all_dates(): void
    {
        $this->makeInbound([$this->line('TUBE', 'TUBE_S', 2)], ['order_date' => '2024-01-01 09:00:00']);
        $this->makeInbound([$this->line('TUBE', 'TUBE_S', 3)], ['order_date' => now()->format('Y-m-d H:i:s')]);

        $result = collect(InboundService::getTotalOfAllInboundProductsv2($this->branchCode))->keyBy('code');

        $this->assertEquals(5, $result['TUBE_S']['quantity']);
    }

    public function test_it_excludes_cancelled_and_deleted_orders(): void
    {
        $this->makeInbound([$this->line('TUBE', 'TUBE_S', 4)]);
        $this->makeInbound([$this->line('TUBE', 'TUBE_S', 99)], ['status' => 'Cancelled']);
        $this->makeInbound([$this->line('TUBE', 'TUBE_S', 99)], ['status' => 'Deleted']);
        $this->makeInbound([$this->line('TUBE', 'TUBE_S', 99)], ['status' => 'Wrong entry']);

        $result = collect(InboundService::getTotalOfAllInboundProducts($this->branchCode))->keyBy('code');

        $this->assertEquals(4, $result['TUBE_S']['quantity']);
    }
}
