<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category = [
            [
                'name' => 'Foods',
            ],
            [
                'name' => 'Automatives',
            ],
            [
                'name' => 'Sports',
            ],
            [
                'name' => 'Pets',
            ],
            [
                'name' => 'Appliances',
            ],
            [
                'name' => 'Electronics',
            ],
        ];
        Category::insert($category);
    }
}
