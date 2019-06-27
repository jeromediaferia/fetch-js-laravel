<?php

use App\Model\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Utilisation de faker
     * @url https://github.com/fzaninotto/Faker
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        for($i = 0; $i < 20; $i++){
            $user = [
                'name' => $faker->name,
                'email' => $faker->email,
                'password' => Hash::make('000000')
            ];
            User::insert($user);
        }
    }
}
