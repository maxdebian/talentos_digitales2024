<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleAdmin = Role::create(['name'=>'admin']);
        $roleClient = Role::create(['name'=>'client']);
        Permission::create(['name'=>'obras_sociales.index']);
        Permission::create(['name'=>'obras_sociales.create']);
        Permission::create(['name'=>'obras_sociales.edit']);
        Permission::create(['name'=>'obras_sociales.show']);
        Permission::create(['name'=>'obras_sociales.update']);
        Permission::create(['name'=>'obras_sociales.destroy']);
        Permission::create(['name'=>'obras_sociales.store']);
        $permissions = Permission::all();
        $roleAdmin->syncPermissions($permissions);
        $roleClient->syncPermissions($permissions);

        $user = User::where('name','admin')->first();
        $user->assignRole($roleAdmin);
    }
}
