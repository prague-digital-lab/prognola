<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ReservationFactory extends Factory
{
    public function definition(): array
    {
        $date = Carbon::now()->addDays(rand(-500, 50));

        return [
            'uuid' => $this->faker->uuid(),
            'date_from' => $date,
            'date_to' => $date->addHours(3),
            'team_count' => $this->faker->randomNumber(1),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'description' => $this->faker->text(),
            'price' => $this->faker->randomNumber(4),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
