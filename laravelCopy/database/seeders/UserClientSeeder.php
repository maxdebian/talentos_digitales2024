<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleClient = Role::where('name','client')->first();

        $user = User::create([
            'name' => 'client',
            'email' => 'client@example.com',
            'email_verified_at' => now(),
            'password'  => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);
        //$user = User::where('name','client')->first();
        $user->assignRole($roleClient);

    }
}
