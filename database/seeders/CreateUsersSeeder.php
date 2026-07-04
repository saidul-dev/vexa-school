<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_information = json_encode([
            'gender' => '',
            'blood_group' => '',
            'birthday' => '',
            'phone' => '',
            'address' => '',
            'photo' => '',
            'school_role' => 0,
        ]);

        $user = [
            [
               'name'=>'Superadmin',
               'email'=>'superadmin@example.com',
               'role_id'=>'1',
               'password'=> bcrypt('1234'),
               'code'=> student_code(),
               'user_information'=> $user_information,
            ],
            [
               'name'=>'Admin',
               'email'=>'admin@example.com',
               'role_id'=>'2',
               'password'=> bcrypt('1234'),
               'code'=> student_code(),
               'user_information'=> $user_information,
            ],
            [
               'name'=>'User',
               'email'=>'student@example.com',
               'role_id'=>'3',
               'password'=> bcrypt('1234'),
               'code'=> student_code(),
               'user_information'=> $user_information,
            ],
        ];
  
        foreach ($user as $key => $value) {
            User::create($value);
        }
    }

}
