<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // First admin account
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name'     => 'Administrator',
                'email'    => null,
                'password' => Hash::make('Admin@1234'),
                'role'     => 'admin',
                'status'   => 'active',
            ]
        );

        // Starter categories
        $categories = [
            ['name' => 'Engineering',       'slug' => 'engineering'],
            ['name' => 'Science',           'slug' => 'science'],
            ['name' => 'Mathematics',       'slug' => 'mathematics'],
            ['name' => 'Computer Science',  'slug' => 'computer-science'],
            ['name' => 'Standards & Codes', 'slug' => 'standards-codes'],
            ['name' => 'Manuals',           'slug' => 'manuals'],
            ['name' => 'Reports',           'slug' => 'reports'],
        ];

        foreach ($categories as $i => $data) {
            Category::firstOrCreate(
                ['slug' => $data['slug']],
                ['name' => $data['name'], 'sort_order' => $i + 1]
            );
        }

        $this->command->info('Admin account: username=admin  password=Admin@1234');
        $this->command->warn('Change the admin password after first login!');
    }
}
