<?php

namespace Database\Factories;

use App\Models\Voucher;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Voucher>
 */
class VoucherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $date = Carbon::now()->subDays(rand(1, 500));

        return [
            'uuid' => $this->faker->uuid(),

            'code' => strtoupper('VB-'.Str::random(8)),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),

            'family_entry_count' => $this->faker->numberBetween(0, 1),
            'child_entry_count' => $this->faker->numberBetween(0, 5),
            'student_entry_count' => $this->faker->numberBetween(0, 3),
            'adult_entry_count' => $this->faker->numberBetween(1, 10),

            'price' => $this->faker->randomNumber(4),
            'internal_note' => $this->faker->text(),
            'description' => $this->faker->text(),

            'order_status' => Voucher::ORDER_STATUS_WAITING_FOR_PAYMENT,
            'shipping_type' => 'email',
            'shipping_status' => Voucher::SHIPPING_STATUS_TO_BE_PRINTED,
            'custom_text' => $this->faker->text(),

            'created_at' => $date,
            'updated_at' => Carbon::now(),
        ];
    }
}
