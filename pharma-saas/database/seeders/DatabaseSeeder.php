<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = DB::table('tenants')->insertGetId([
            'name' => 'Demo Pharmacy',
            'slug' => 'demo-pharmacy',
            'domain' => 'demo.pharmasaas.local',
            'status' => 'active',
            'subscription_start' => now(),
            'subscription_end' => now()->addYear(),
            'subscription_plan' => 'premium',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $adminRoleId = DB::table('roles')->insertGetId([
            'tenant_id' => $tenantId,
            'name' => 'Admin',
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cashierRoleId = DB::table('roles')->insertGetId([
            'tenant_id' => $tenantId,
            'name' => 'Cashier',
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $userId = DB::table('users')->insertGetId([
            'tenant_id' => $tenantId,
            'name' => 'Admin User',
            'email' => 'admin@pharmasaas.com',
            'password' => Hash::make('password'),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('model_has_roles')->insert([
            'role_id' => $adminRoleId,
            'model_type' => 'App\Models\User',
            'model_id' => $userId,
        ]);

        $permissions = [
            'manage_products',
            'manage_stock',
            'manage_sales',
            'manage_transfers',
            'manage_purchases',
            'manage_users',
            'manage_settings',
            'view_reports',
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert([
                'tenant_id' => $tenantId,
                'name' => $permission,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('role_has_permissions')
            ->where('role_id', $adminRoleId)
            ->delete();

        $permissionIds = DB::table('permissions')
            ->where('tenant_id', $tenantId)
            ->pluck('id');

        foreach ($permissionIds as $permissionId) {
            DB::table('role_has_permissions')->insert([
                'permission_id' => $permissionId,
                'role_id' => $adminRoleId,
            ]);
        }

        $categoryId = DB::table('categories')->insertGetId([
            'tenant_id' => $tenantId,
            'name' => 'Medicaments',
            'code' => 'MED',
            'description' => 'General Medicines',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('categories')->insert([
            'tenant_id' => $tenantId,
            'name' => 'Antibiotiques',
            'code' => 'AB',
            'parent_id' => $categoryId,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('categories')->insert([
            'tenant_id' => $tenantId,
            'name' => 'Analgesiques',
            'code' => 'AN',
            'parent_id' => $categoryId,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $supplierId = DB::table('suppliers')->insertGetId([
            'tenant_id' => $tenantId,
            'name' => 'Pharma Distribution',
            'code' => 'PD001',
            'email' => 'contact@pharmadistribution.mg',
            'phone' => '+261 32 12 345 67',
            'address' => 'Antananarivo, Madagascar',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $productId = DB::table('products')->insertGetId([
            'tenant_id' => $tenantId,
            'name' => 'Paracetamol 500mg',
            'code' => 'PAR500',
            'category_id' => $categoryId,
            'supplier_id' => $supplierId,
            'purchase_price' => 2500,
            'selling_price' => 3500,
            'min_stock' => 100,
            'max_stock' => 1000,
            'unit' => 'tablet',
            'dosage' => '500mg',
            'form' => 'Tablet',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('stocks')->insert([
            'tenant_id' => $tenantId,
            'product_id' => $productId,
            'quantity' => 500,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('batches')->insert([
            'tenant_id' => $tenantId,
            'product_id' => $productId,
            'batch_number' => 'BATCH001',
            'expiry_date' => now()->addYear(),
            'quantity' => 500,
            'purchase_price' => 2500,
            'selling_price' => 3500,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('warehouses')->insert([
            'tenant_id' => $tenantId,
            'name' => 'Main Warehouse',
            'code' => 'WH001',
            'is_default' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('settings')->insert([
            'tenant_id' => $tenantId,
            'key' => 'app_name',
            'value' => 'Pharma SaaS',
            'type' => 'string',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('settings')->insert([
            'tenant_id' => $tenantId,
            'key' => 'currency',
            'value' => 'MGA',
            'type' => 'string',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('settings')->insert([
            'tenant_id' => $tenantId,
            'key' => 'language',
            'value' => 'fr',
            'type' => 'string',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('settings')->insert([
            'tenant_id' => $tenantId,
            'key' => 'auto_backup',
            'value' => 'true',
            'type' => 'boolean',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('settings')->insert([
            'tenant_id' => $tenantId,
            'key' => 'stock_refresh_interval',
            'value' => '5',
            'type' => 'integer',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
