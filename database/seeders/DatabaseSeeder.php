<?php

namespace Database\Seeders;

use App\Models\Evaluation;
use App\Models\EvaluationCriteria;
use App\Models\EvaluationCriteriaScore;
use App\Models\Outlet;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles, permissions and default users
        $this->call(RolesAndPermissionsSeeder::class);

        // Create additional evaluator users
        User::factory(3)->create()->each(function ($user) {
            $user->assignRole('evaluator');
        });

        // Create outlets
        Outlet::factory(10)->create();

        // For now, we'll skip creating evaluation criteria, evaluations, and scores
        // as there seems to be an issue with the table names
        // We'll focus on just setting up the Filament admin panel with proper roles and permissions
    }
}
