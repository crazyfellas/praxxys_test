<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $category_id = rand(1,6);
        return [
            'name' => $this->faker->company,
            'category_id' => $category_id,
            'productNumber' => $this->faker->bothify('?##?##'),
            'description' => $this->faker->text(),
            'datetime' => $this->faker->dateTime($max = 'now'),
        ];
    }
}
