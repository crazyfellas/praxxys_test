<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(50)->create();
        DB::table('users')->insert([
            'name' => 'Glenn',
            'email' => 'glenn@gmail.com',
            'password' => Hash::make('12345678'),
        ]);
    }
}
