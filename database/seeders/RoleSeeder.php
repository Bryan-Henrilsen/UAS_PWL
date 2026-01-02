<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
//
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset Cache Permission (Penting)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Buat Daftar Permissions (Hak Akses Spesifik)
        $permissions = [
            // Dashboard
            'view_dashboard',
            'view_financials', // Melihat Rupiah/Aset (Hanya Super Admin)
            
            // Produk (Master Data)
            'manage_products', // CRUD Produk & Varian (Supervisor)
            
            // Inbound (Masuk)
            'create_inbound',  // Staff Inbound
            'approve_inbound', // Supervisor
            
            // Outbound (Keluar)
            'create_outbound', // Staff Outbound
            'approve_outbound',// Supervisor
            
            // Stock Opname (SO)
            'create_so',       // Semua Staff & Supervisor (Input Fisik)
            'approve_so',      // Supervisor (Update Stok Master)
            
            // User Management
            'manage_users',    // Super Admin
        ];

        // Create Permissions ke Database
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 3. Buat Role & Assign Permission

        // A. STAFF INBOUND
        $roleInbound = Role::create(['name' => 'Staff Inbound']);
        $roleInbound->givePermissionTo([
            'view_dashboard',
            'create_inbound',
            'create_so',
        ]);

        // B. STAFF OUTBOUND
        $roleOutbound = Role::create(['name' => 'Staff Outbound']);
        $roleOutbound->givePermissionTo([
            'view_dashboard',
            'create_outbound',
            'create_so',
        ]);

        // C. SUPERVISOR
        $roleSpv = Role::create(['name' => 'Supervisor']);
        $roleSpv->givePermissionTo([
            'view_dashboard',
            'manage_products',
            'approve_inbound',
            'approve_outbound',
            'create_so',
            'approve_so',
        ]);

        // D. SUPER ADMIN (God Mode)
        $roleAdmin = Role::create(['name' => 'Super Admin']);
        // Super Admin punya semua permission yang ada
        $roleAdmin->givePermissionTo(Permission::all());


        // 4. (Opsional) Bikin Akun Dummy untuk Testing
        // Biar Anda tidak capek register manual satu-satu
        
        // Akun Admin
        $admin = User::create([
            'name' => 'Owner Toko',
            'username' => 'owner',
            'password' => bcrypt('12345'),
        ]);
        $admin->assignRole('Super Admin');

        // Akun SPV
        $spv = User::create([
            'name' => 'Bryan Henrilsen',
            'username' => 'Bryan SPV',
            'password' => bcrypt('12345'),
        ]);
        $spv->assignRole('Supervisor');

        // Akun Staff Masuk
        $staffIn = User::create([
            'name' => 'Dhavid Bhertus',
            'username' => 'Dhavid Inbound',
            'password' => bcrypt('12345'),
        ]);
        $staffIn->assignRole('Staff Inbound');

        // Akun Staff Keluar
        $staffOut = User::create([
            'name' => 'Felix Filbert',
            'username' => 'Felix Outbound',
            'password' => bcrypt('12345'),
        ]); 
        $staffOut->assignRole('Staff Outbound');
    }
}