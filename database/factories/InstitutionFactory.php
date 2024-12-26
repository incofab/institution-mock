<?php
namespace Database\Factories;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

class InstitutionFactory extends Factory
{
    function definition()
    {
        $admin = User::query()
            ->where('email', config('app.admin.email'))
            ->first();
        return [
            'created_by_user_id' => $admin ?? User::factory()->admin(),
            'code' => fake()->unique()->randomNumber(5, true),
            'name' => fake()->company,
            'address' => fake()->address,
            'phone' => fake()->numerify('###########'),
            'email' => fake()->unique()->safeEmail,
            'status' => 'active',
        ];
    }

    function user(User $user = null)
    {
        return $this->afterCreating(function (Institution $model) use ($user) {
            $user = $user ?? User::factory()->create();
            $model->institutionUsers()->firstOrCreate(['user_id' => $user->id]);
        });
    }
}
