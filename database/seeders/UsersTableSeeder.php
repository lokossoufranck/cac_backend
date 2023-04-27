<?php


namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->where('email', 'admin@amehouga.com')->delete();

        DB::table('users')->insert([
            'name' => 'CAC',
            'firstname' => 'cac',
            'username' => 'cac',
            'email' => 'admin@amehouga.com',
            'password' => bcrypt('12345678'),
            'type' => 'admin',
        ]);
    }
}
