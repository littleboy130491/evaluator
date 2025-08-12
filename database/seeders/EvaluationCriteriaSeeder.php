<?php

namespace Database\Seeders;

use App\Models\EvaluationCriteria;
use Illuminate\Database\Seeder;

class EvaluationCriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $criteria = [
            [
                'name' => 'Customer Service',
                'description' => 'Quality of customer interactions, responsiveness, and problem resolution',
                'max_score' => 10,
                'category' => 'Service Quality',
                'is_active' => true,
            ],
            [
                'name' => 'Cleanliness & Hygiene',
                'description' => 'Overall cleanliness of the outlet, hygiene standards, and maintenance',
                'max_score' => 10,
                'category' => 'Operations',
                'is_active' => true,
            ],
            [
                'name' => 'Product Quality',
                'description' => 'Quality and consistency of products offered',
                'max_score' => 10,
                'category' => 'Product Management',
                'is_active' => true,
            ],
            [
                'name' => 'Staff Performance',
                'description' => 'Staff efficiency, knowledge, and professionalism',
                'max_score' => 10,
                'category' => 'Human Resources',
                'is_active' => true,
            ],
            [
                'name' => 'Inventory Management',
                'description' => 'Stock levels, product availability, and inventory organization',
                'max_score' => 10,
                'category' => 'Operations',
                'is_active' => true,
            ],
            [
                'name' => 'Safety Compliance',
                'description' => 'Adherence to safety protocols and regulations',
                'max_score' => 10,
                'category' => 'Compliance',
                'is_active' => true,
            ],
            [
                'name' => 'Financial Performance',
                'description' => 'Sales targets, profit margins, and financial efficiency',
                'max_score' => 10,
                'category' => 'Financial',
                'is_active' => true,
            ],
            [
                'name' => 'Marketing & Promotion',
                'description' => 'Implementation of marketing initiatives and promotional activities',
                'max_score' => 10,
                'category' => 'Marketing',
                'is_active' => true,
            ],
            [
                'name' => 'Technology Usage',
                'description' => 'Effective use of POS systems, digital tools, and technology adoption',
                'max_score' => 10,
                'category' => 'Technology',
                'is_active' => true,
            ],
            [
                'name' => 'Brand Standards',
                'description' => 'Adherence to brand guidelines, visual standards, and corporate identity',
                'max_score' => 10,
                'category' => 'Brand Management',
                'is_active' => true,
            ],
        ];

        foreach ($criteria as $criterion) {
            EvaluationCriteria::create($criterion);
        }
    }
}