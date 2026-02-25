<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Permissions
        $permissions = [
            'manage_users',
            'manage_locations',
            'manage_extinguishers',
            'manage_reports',
            'view_all_dashboards',
            'export_reports',
            'manage_system_settings',
            
            'perform_inspections',
            'scan_qr_code',
            'upload_photos',
            'manage_own_repair_logs',
            'view_reports',
            'view_dashboards',
            'manage_repair_logs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $safetyRole = Role::firstOrCreate(['name' => 'safety_officer']);

        // Assign Permissions
        $adminRole->givePermissionTo(Permission::all());
        
        $safetyRole->givePermissionTo([
            'perform_inspections',
            'scan_qr_code',
            'upload_photos',
            'manage_own_repair_logs',
            'view_reports',
            'view_dashboards',
        ]);
        
        // Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin System',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );
        $admin->assignRole($adminRole);

        $safety = User::firstOrCreate(
            ['email' => 'safety@example.com'],
            [
                'name' => 'สมชาย ปลอดภัย',
                'password' => Hash::make('password'),
                'role' => 'safety_officer',
            ]
        );
        $safety->assignRole($safetyRole);
    }
}
