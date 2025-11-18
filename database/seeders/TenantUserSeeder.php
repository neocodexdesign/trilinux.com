<?php

// database/seeders/TenantUserSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TenantUserSeeder extends Seeder
{
    public function run(): void
    {
        // cria tenant (id = slug é ótimo para PATH tenancy)
        $tenant = Tenant::firstOrCreate(['id' => 'neocodexlabs'], [
            'data' => ['name' => 'Neocodex Labs', 'plan' => 'pro']
        ]);

        // cria user (ou pega existente)
        $user = User::firstOrCreate(['email' => 'owner@neocodexlabs.com'], [
            'name' => 'Owner Neo',
            'password' => Hash::make('secret123'),
        ]);

        // vincula no pivot com papel
        $user->tenants()->syncWithoutDetaching([
            $tenant->id => ['role' => 'owner'],
        ]);
    }
}