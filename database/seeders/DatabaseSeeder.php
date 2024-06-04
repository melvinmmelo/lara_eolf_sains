<?php

namespace Database\Seeders;

use App\Models\Branches;
use App\Models\CompanyDetails;
use App\Models\Customers;
use App\Models\Delivery;
use App\Models\DeliveryPurchaseReceipt;
use App\Models\Drivers;
use App\Models\Equipment;
use App\Models\pricelevels;
use App\Models\prices;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Vehicles;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        CompanyDetails::create([
            'name' => 'EOLF'
        ]);

        $user = User::factory()->create([
            'last_name' => 'Melo',
            'first_name' => 'Melvin',
            'contact_no' => '09621235214',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('eolf@2024'),
        ]);

        // create roles
        Role::create(['name' => 'staff']);
        Role::create(['name' => 'driver']);

        $role = Role::create(['name' => 'admin']);

        // create permissions
        $permission = Permission::create(['name' => 'add sales']);

        // give role permission
        $role->givePermissionTo($permission);

        // assign user melvin as admin
        $user->assignRole('admin');


        Drivers::create([
            'name' => "Froilan Andal",
            'address' => 'Batangas',
            'contact' => '09253652321',
            'status' => 'Active',
            'default_price_level' => '1',

        ]);

        Vehicles::create([
            'plateno' => "MNM-123",
            'brand' => 'Truck',
            'description' => 'Small Truck',
            'status' => 'Active',
        ]);

        ProductType::create(
            [
                'code' => "SC",
                'name' => 'Small Cup',
                'volume' => '150',
                'bo_pricing' => '',
                'spoon_pcs_per_bag' => '60',
                'is_active' => '1',
            ]
        );

        ProductType::create(
            [
                'code' => "MC",
                'name' => 'Medium Cup',
                'volume' => '150',
                'bo_pricing' => '',
                'spoon_pcs_per_bag' => '35',
                'is_active' => '1',
            ]
        );

        ProductVariant::create([
            'code' => "RR",
            'name' => 'Rocky Road',
            'is_active' => '1',
        ]);

        ProductVariant::create([
            'code' => "SB",
            'name' => 'Strawberry',
            'is_active' => '1',
        ]);

        Product::create(
            [
                'code' => "SC_RR",
                'product_type_code' => 'SC',
                'product_variant_code' => 'RR',
                'is_active' => '1',
            ]
        );

        Product::create(
            [
                'code' => "SC_SB",
                'product_type_code' => 'SC',
                'product_variant_code' => 'SB',
                'is_active' => '1',
            ]
        );

        Product::create(
            [
                'code' => "MC_SB",
                'product_type_code' => 'MC',
                'product_variant_code' => 'SB',
                'is_active' => '1',
            ]
        );

        pricelevels::create([
            'branch_code' => "EFTO-CAG",
            'pl_name' => "SUMMER 2024",
            'pl_desc' => 'FP Summer 2024',
            'pl_status' => 'Active',
            'pl_type' => 'CUSTOMER'
        ]);

        pricelevels::create([
            'branch_code' => "EFTO-CAG",
            'pl_name' => "FACTORY PRICE",
            'pl_desc' => '2024',
            'pl_status' => 'Active',
            'pl_type' => 'FACTORY PRICE'
        ]);


        prices::create([
            'pricelevel_id' => "1",
            'p_code' => 'SC_RR',
            'p_unit' => 'Bag',
            'p_quant' => 5,
            'p_price' => 150,
        ]);

        prices::create([
            'pricelevel_id' => "1",
            'p_code' => 'SC_SB',
            'p_unit' => 'Bag',
            'p_quant' => 15,
            'p_price' => 180,
        ]);

        prices::create([
            'pricelevel_id' => "2",
            'p_code' => 'SC_RR',
            'p_unit' => 'Bag',
            'p_quant' => 5,
            'p_price' => 100,
        ]);

        prices::create([
            'pricelevel_id' => "2",
            'p_code' => 'SC_SB',
            'p_unit' => 'Bag',
            'p_quant' => 15,
            'p_price' => 120,
        ]);

        Branches::create([
            'code' => "EFTO-CAG",
            'name' => "EOLF Food Trading OPC - Cagayan Valley",
            'address' => 'Tuguegarao City, Cagayan',
            'office_no' =>  '09123456789',
        ]);

        Branches::create([
            'code' => "EFTO-TAR",
            'name' => "EOLF Food Trading OPC - Tarlac",
            'address' => 'Tarlac',
            'office_no' =>  '09123456789',
        ]);

        Equipment::create([
            'ownership' => "Not-Owned",
            'type' => "Chest Freezer",
            'brand' => 'CONDURA',
            'price' =>  '42000.00',
            'price' =>  '42000.00',
            'serial_no' => 'ABC123',
            'code' => '123456',
        ]);

        DeliveryPurchaseReceipt::create([
            'branch_code' => "EFTO-CAG",
            'dr_no' => "DR-2024-001",
            'issue_date' => '2024-01-01',
            'status' => 'Encoding',
            'user_id' => 1
        ]);
    }
}
