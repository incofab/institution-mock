<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->seedAdmin();
        // $this->call([
        // UserSeeder::class,
        // ExamContentSeeder::class,
        // CourseSeeder::class,
        // CourseSessionSeeder::class,
        // QuestionSeeder::class,
        // SubscriptionPlanSeeder::class,
        // ]);
    }

    function seedAdmin()
    {
        User::query()->firstOrCreate(
            ['email' => config('app.admin.email')],
            [
                'phone' => '09033229933',
                'name' => 'Admin Admin',
                'password' => Hash::make('password'),
            ],
        );
    }
}
