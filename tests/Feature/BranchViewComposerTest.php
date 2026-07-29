<?php

namespace Tests\Feature;

use App\Models\Branches;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * The global view()->composer('*') in AppServiceProvider resolves the session
 * branch on every render. It used to dereference the result unguarded, so a
 * session pointing at a branch that no longer exists produced a 500 on every
 * page — and, because error pages render through the same composer, it masked
 * authorization failures as server errors.
 */
class BranchViewComposerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        Role::findOrCreate('admin', 'web');
        $user = User::factory()->create();
        $user->assignRole('admin');

        return $user;
    }

    public function test_a_session_branch_that_no_longer_exists_does_not_error(): void
    {
        $this->admin();

        // No branches row matches this code.
        session(['branch_code' => 'GONE-BRANCH']);

        $this->actingAs($this->admin())
            ->get(route('report.sales-by-product-type'))
            ->assertOk();
    }

    public function test_branch_name_is_blank_rather_than_null_for_a_stale_branch(): void
    {
        session(['branch_code' => 'GONE-BRANCH']);

        $response = $this->actingAs($this->admin())->get(route('report.sales-by-product-type'));

        $this->assertSame('', $response->viewData('branch_name'));
    }

    public function test_a_valid_session_branch_still_resolves_its_name(): void
    {
        Branches::create([
            'code' => 'EFTO-CAG',
            'name' => 'EOLF Food Trading OPC - Cagayan Valley',
            'address' => 'Test Address',
            'office_no' => '000',
        ]);

        session(['branch_code' => 'EFTO-CAG']);

        $response = $this->actingAs($this->admin())->get(route('report.sales-by-product-type'));

        $this->assertSame('EOLF Food Trading OPC - Cagayan Valley', $response->viewData('branch_name'));
    }
}
