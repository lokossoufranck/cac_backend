<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FormatsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('formats')->truncate();

        DB::table('formats')->insert(
            [
                [
                    'name' => 'pdf',
                    'code' => 'pdf',
                    'status' => 1
                ],
                [
                    'name' => 'image',
                    'code' => 'image',
                    'status' => 1
                ],
                [
                    'name' => 'web',
                    'code' => 'web',
                    'status' => 1
                ],
            ]
        );
    }
}
