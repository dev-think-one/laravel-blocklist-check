<?php

namespace LaraBlockList\Tests\Fixtures\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use LaraBlockList\Tests\Fixtures\Models\User;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name'   => $this->faker->name(),
            'email'  => $this->faker->email(),
        ];
    }
}
