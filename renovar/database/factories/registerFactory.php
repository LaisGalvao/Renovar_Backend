<?php

namespace Database\Factories;

use App\Models\register;
use Illuminate\Database\Eloquent\Factories\Factory;

class registerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = register::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            'nome' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'CPF' =>  $this->faker->randomDigit(),
            'telefone' => $this->faker->phoneNumber(),
            'idade' => $this->faker->randomDigit(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ];
    }
}
