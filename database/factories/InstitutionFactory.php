<?php
namespace Database\Factories;

use App\Models\Course;
use App\Models\Grade;
use App\Models\Institution;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InstitutionFactory extends Factory
{
  function definition()
  {
    $admin = User::query()->where('email', config('app.admin.email'))->first();
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
  function students($count = 2, ?Grade $grade = null)
  {
    return $this->afterCreating(
      fn(Institution $model) => Student::factory($count)
        ->when(
          $grade,
          fn($q) => $q->grade($grade),
          fn($q) => $q->institution($model),
        )
        ->create(),
    );
  }
  function courses($count = 2)
  {
    return $this->afterCreating(
      fn(Institution $model) => Course::factory($count)
        ->courseSessions(4)
        ->institution($model)
        ->create(),
    );
  }
}
