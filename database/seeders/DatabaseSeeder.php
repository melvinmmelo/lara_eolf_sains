<?php

namespace Database\Seeders;

use App\Models\CompanyDetails;
use App\Models\Delivery;
use App\Models\Drivers;
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
                'is_active' => 'on',
            ],
            [
                'code' => "MC",
                'name' => 'Medium Cup',
                'volume' => '150',
                'bo_pricing' => '',
                'is_active' => 'on',
            ]
        );

        ProductType::create(
            [
                'code' => "MC",
                'name' => 'Medium Cup',
                'volume' => '150',
                'bo_pricing' => '',
                'is_active' => 'on',
            ]
        );

        ProductVariant::create([
            'code' => "RR",
            'name' => 'Rocky Road',
            'is_active' => 'on',
        ]);

        ProductVariant::create([
            'code' => "SB",
            'name' => 'Strawberry',
            'is_active' => 'on',
        ]);

        Product::create(
            [
                'code' => "SC_RR",
                'product_type_code' => 'SC',
                'product_variant_code' => 'RR',
                'is_active' => 'on',
            ],
            [
                'code' => "SC_SB",
                'product_type_code' => 'SC',
                'product_variant_code' => 'SB',
                'is_active' => 'on',
            ],
            [
                'code' => "MC_SB",
                'product_type_code' => 'MC',
                'product_variant_code' => 'SB',
                'is_active' => 'on',
            ]
        );

        Product::create(
            [
                'code' => "SC_SB",
                'product_type_code' => 'SC',
                'product_variant_code' => 'SB',
                'is_active' => 'on',
            ]
        );

        Product::create(
            [
                'code' => "MC_SB",
                'product_type_code' => 'MC',
                'product_variant_code' => 'SB',
                'is_active' => 'on',
            ]
        );

        pricelevels::create([
            'pl_name' => "FACTORY PRICE",
            'pl_desc' => 'FP Summer 2024',
            'pl_status' => 'Active',
        ]);


        prices::create([
            'p_level' => "FACTORY PRICE",
            'p_code' => 'SC_RR',
            'p_unit' => 'Bag',
            'p_quant' => 5,
            'p_price' => 150,
        ]);
    }
}
