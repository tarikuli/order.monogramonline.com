<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    protected $users = [
        [
            'sirajul',
            'sirajul.islam.anik@gmail.com',
            '123456',
            1212,
            121212,
            1,
            1, // role id
        ],
        [
            'Jewel',
            'jewel@monogramonline.com',
            '123456',
            1212,
            121212,
            1,
            1, // role id
        ],
        [
            'erika@dealtowin.com',
            'erika@dealtowin.com',
            '123456',
            1212,
            121212,
            1,
            9, // role id
        ],
        [
            'Alina',
            'alina@monogramonline.com',
            '123456',
            1212,
            121212,
            1,
            1, // role id
        ],
        [
            'Frank',
            'frank.cs@monogramonline.com',
            '123456',
            1212,
            121212,
            1,
            4, // role id
        ],
        [
            'Alysse1@monogramonline.com',
            'alysse1@monogramonline.com',
            '123456',
            1212,
            121212,
            1,
            2, // role id
        ],
        [
            'Bessy1@monogramonline.com',
            'bessy1@monogramonline.com',
            '123456',
            1212,
            121212,
            1,
            6, // role id
        ],
        [
            'camila@dealtowin.com',
            'camila@dealtowin.com',
            '123456',
            1212,
            121212,
            1,
            8, // role id
        ],
        [
            'Danielle',
            'danielle@dealtowin.com',
            '123456',
            1212,
            121212,
            1,
            6, // role id
        ],
        [
            'Dhara',
            'dhara.sp@monogramonline.com',
            '123456',
            1212,
            121212,
            1,
            9, // role id
        ],
        [
            'Eddie',
            'eddie@monogramonline.com',
            '123456',
            1212,
            121212,
            1,
            5, // role id
        ],
        [
            'Emma - Shipper Ready TO SHIP',
            'emma@dealtowin.com',
            '123456',
            1212,
            121212,
            1,
            5, // role id
        ],
    ];
    public function run ()
    {
        foreach ($this->users as $value) {
            $user = new User();
            $user->username = $value[0];
            $user->email = $value[1];
            $user->password = $value[2];
            $user->vendor_id = $value[3];
            $user->zip_code = $value[4];
            $user->state = $value[5];
            $user->save();
            $user->attachRole($value[6]);
        }
        

    }
}
