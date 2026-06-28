<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Problem;
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

        Problem::query()->delete();
        // $this->call(JavaScriptSeeder::class);
        // $this->call(MathSeeder::class);
        // $this->call(AlgorithmsSeeder::class);
        // $this->call(AISeeder::class);
        // $this->call(VanadiumKnowledgeSeeder::class);
        // $this->call(RefractoryGoldKnowledgeSeeder::class);
        // $this->call(VanadiumVsLithiumSeeder::class);
        // $this->call(VanadiumVsOtherBatteriesSeeder::class);
        // $this->call(VRFBInnovativeTechnologySeeder::class);
        // $this->call(VRFBBuildPreparationSeeder::class);
        // $this->call(PlanWorkReportKnowledgeSeeder::class);
        // $this->call(HeatResistantPaintKnowledgeSeeder::class);
        // $this->call(MoisturizingAgentsKnowledgeSeeder::class);
        // $this->call(InorganicSpecialPaintsKnowledgeSeeder::class);
        // $this->call(HairDyeTypesKnowledgeSeeder::class);
        // $this->call(FoundationMilkKnowledgeSeeder::class);
        // $this->call(CosmeticsKnowledgeSeeder::class);
        // $this->call(IndustrialChemistrySeeder::class);
        // $this->call(EnergyStorageSeeder::class);
        // $this->call(BusinessStrategyKnowledgeSeeder::class);

        $this->command->info('Admin account: username=admin  password=Admin@1234');
        $this->command->warn('Change the admin password after first login!');
    }
}
