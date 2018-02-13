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

        $pwd = bcrypt('pqsszord');
        $u = factory(App\Models\User::class)->create([
            'email' => 'john.doe@example.com',
            'username' => 'john_doe',
            'password' => $pwd,
            'activated' => true,
            'remember_token' => null,
        ]);
        factory(App\Models\Person::class)->create([
            'person_first_name' => 'John',
            'person_last_name' => 'Doe',
            'user_id' => $u->getAttribute('user_id')
        ]);

        $u = factory(App\Models\User::class)->create([
            'email' => 'jane.doe@example.com',
            'username' => 'jane_doe',
            'password' => $pwd,
            'activated' => true,
            'remember_token' => null,
        ]);

        factory(App\Models\Person::class)->create([
            'person_first_name' => 'Jane',
            'person_last_name' => 'Doe',
            'user_id' => $u->getAttribute('user_id')
        ]);

        $faker = Faker\Factory::create();

        for ($i = 1; $i <= 50; $i++) {
            $fn = $faker->firstName;
            $ln = $faker->lastName;

            $u = factory(App\Models\User::class)->create([
                'username' => substr(strtolower(substr($fn, 0, 1) . '_' . $ln), 0, 15),
                'activated' => true,
                'email' => $faker->unique()->safeEmail,
                'password' => $pwd,
            ]);
            factory(App\Models\Person::class)->create([
                'person_first_name' => $fn,
                'person_last_name' => $ln,
                'user_id' => $u->getAttribute('user_id')
            ]);
        }
    }
}
