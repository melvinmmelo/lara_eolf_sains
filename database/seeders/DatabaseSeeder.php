<?php

namespace Database\Seeders;

use App\Models\User;
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

    }
}
