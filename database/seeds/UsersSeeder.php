<?php

use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->image = new \App\Support\Providers\Image();
        $pwd = bcrypt('secret');
        $u = factory(App\Models\User::class)->create([
            'email' => 'john.doe@example.com',
            'username' => 'john_doe',
            'password' => $pwd,
            'activated' => true,
            'remember_token' => null,
        ]);
        $this->createAvatar('john_doe', 'John Doe');
        factory(App\Models\Person::class)->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'user_id' => $u->getAttribute('user_id')
        ]);
        factory(App\Models\GroupMember::class)->create([
            "group_id" => 2,
            'user_id' => $u->getAttribute('user_id')
        ]);
        factory(App\Models\GroupMember::class)->create([
            "group_id" => 4,
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
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'user_id' => $u->getAttribute('user_id')
        ]);

        $this->createAvatar('jane_doe', 'Jane Doe');

        factory(App\Models\GroupMember::class)->create([
            "group_id" => 2,
            'user_id' => $u->getAttribute('user_id')
        ]);
        factory(App\Models\GroupMember::class)->create([
            "group_id" => 4,
            'user_id' => $u->getAttribute('user_id')
        ]);

        $faker = Faker\Factory::create();

        $usernames = [];
        for ($i = 1; $i <= 500; $i++) {
            $fn = $faker->firstName;
            $ln = $faker->lastName;
            $username = substr(strtolower(substr($fn, 0, 1) . '_' . $ln), 0, 15);

            if (isset($usernames[$username])) {
                $usernames[$username]++;
            } else {
                $usernames[$username] = 0;
            }

            $u = factory(App\Models\User::class)->create([
                'username' => ($usernames[$username] == 0) ? $username : $username . $usernames[$username],
                'activated' => true,
                'email' => sprintf('%s.%s@%s', strtolower($fn), strtolower($ln), $faker->freeEmailDomain),
                'password' => $pwd,
            ]);
            factory(App\Models\Person::class)->create([
                'first_name' => $fn,
                'last_name' => $ln,
                'user_id' => $u->getAttribute('user_id'),
                'created_at' => $faker->dateTimeBetween('-2 years')
            ]);

//            $this->createAvatar($username, sprintf('%s %s', $fn, $ln));

            if ($i % 50 == 0) {
                $groupID = 3;
                factory(App\Models\GroupMember::class)->create([
                    "group_id" => 4,
                    'user_id' => $u->getAttribute('user_id')
                ]);
            } else {
                $groupID = 4;
            }
            factory(App\Models\GroupMember::class)->create([
                "group_id" => $groupID,
                'user_id' => $u->getAttribute('user_id')
            ]);

        }
    }

    public function createAvatar($username, $name)
    {
        $f = new \App\Support\Media\GeneratedAvatar($username, $name, 'users', 'image_avatar');
        $f->processAvatar();
        $this->image->saveAvatar(\App\Models\Entity::USERS, $f);

    }
}
