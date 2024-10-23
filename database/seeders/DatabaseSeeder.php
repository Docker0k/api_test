<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);

        $admin = User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@admin.com',
        ]);
        $admin->assignRole('admin');

        $users = User::factory(10)->create();
        $users->each(function ($user) {
          $user->assignRole('user');
          Category::create([
              'user_id'=> $user->id,
              'name' => 'Urgent tasks',
              'can_modify' => false
          ]);
        });

    }
}
