<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Addresses;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class AddressesFactory extends Factory
{
    protected $model = Addresses::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'postal_code' => $this->faker->numerify('####'),
            'city' => $this->faker->city(),
            'public_space' => $this->faker->streetName().' '.$this->faker->streetSuffix(),
            'house_number' => $this->faker->numerify('##'),
        ];
    }
}

