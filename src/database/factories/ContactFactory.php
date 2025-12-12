<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'category_id' => $this->faker->numberBetween(1,5),
            'first_name' => $this->faker->lastName,
            'last_name' => $this->faker->firstName,
            'gender' => $this->faker->randomElement([1,2,3]),
            'email' => $this->faker->unique()->safeEmail,
            'tell' => $this->faker->phoneNumber(),
            'address' =>$this->faker->city() .$this->faker->streetAddress(),
            'building' => $this->faker->secondaryAddress,
            'detail' => $this->faker->text(120)
        ];
    }
}
