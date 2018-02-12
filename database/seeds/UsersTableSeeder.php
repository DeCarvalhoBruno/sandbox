<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

//        $faker = Faker\Factory::create();

        factory(App\User::class)->create([
            'name' => 'system',
            'email' => 'system@localhost.local',
            'password' => bcrypt(str_random(15)),
            'remember_token' => null,
        ]);

        factory(App\User::class)->create([
            'name' => 'JohnDoe',
            'email' => 'john.doe@example.com',
            'password' => bcrypt('pqsszord'),
            'remember_token' => null,
        ]);

        factory(App\User::class)->create([
            'name' => 'JaneDoe',
            'email' => 'jane.doe@example.com',
            'password' => bcrypt('pqsszord'),
            'remember_token' => null,
        ]);



    }
}
