<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3),
            'status' => $this->faker->randomElement(['pending', 'in-progress', 'completed']),
            'user_id' => User::factory(),
        ];
    }
}
