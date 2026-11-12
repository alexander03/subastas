<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //'name' => '$this->faker->name()',
            //'email' => $this->faker->unique()->safeEmail(),
            'nombres' => 'Super',
            'apellidopaterno' => 'Adminsitrador',
            'apellidomaterno' => 'Sistemas',
            'dni' => '00000000',
            'telefono' => '',
            'direccion' => '',
            'email' => 'admin@garzasoft.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'tipousuario_id' => '1',
            'distrito_id' => '140101',
            'fechanacimiento' => '2023-08-01'
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
