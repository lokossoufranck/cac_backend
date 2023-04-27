<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BanquesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('banques')->truncate();

        DB::table('banques')->insert(
            [
                [
                    'nom' => 'Bank Of Africa (BOA)',
                    'numero_de_compte' => 'xxx'
                ],
                [
                    'name' => 'United Bank of Africa (UBA)',
                    'numero_de_compte' => 'xxx'
                ],
            ]
        );
    }
}
