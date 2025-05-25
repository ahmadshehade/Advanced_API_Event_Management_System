<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin'],
            [
                'name' => 'admin',
                'password' => Hash::make('123123123'),
            ]
        );

        // Create Roles
        $adminRole = Role::firstOrCreate(['name' => 'adminRole', 'guard_name' => 'api']);
        $userRole = Role::firstOrCreate(['name' => 'userRole', 'guard_name' => 'api']);

        // Permissions for admin (includes all reservation permissions)
        $adminPermissions = [
            // Event Type
            'create type', 'update type', 'delete type','view eventType','view all eventType',

            // Events
            'create event', 'edit event', 'delete event', 'view event',

            // Location
            'create location', 'edit location', 'delete location', 'view location','view all locations',

            // Users
            'view users', 'edit users', 'delete users', 'register users',

            // Images
            'upload image', 'delete image',

            // Reservations
            'view reservations',
            'create reservation',
            'cancel reservation',
            'delete reservation',
            'edit reservation',
        ];

        foreach ($adminPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']);
        }

        // Assign all admin permissions to Admin Role
        $adminRole->syncPermissions($adminPermissions);

        // Assign Admin Role to admin user
        if (!$admin->hasRole('adminRole')) {
            $admin->assignRole($adminRole);
        }

        // Permissions for User Role (restricted to own reservations logically via policies)
        $userPermissions = [
            'view eventType',
            'view all eventType',
            //event
            'view event',
            'view all event',
            //location
            'view location',
            'view all locations',
            //reservation
            'view reservations',      
            'create reservation',
            'edit reservation',       
            'cancel reservation',   
                     
          
        ];

        foreach ($userPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']);
        }

        // Assign user permissions to user role
        $userRole->syncPermissions($userPermissions);
    }
}

