<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ActivitesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activites')->truncate();

        DB::table('activites')->insert(
            [
                [
                    'nom' => 'Réception'
                ],
                [
                    'nom' => 'Emission'
                ],
                [
                    'nom' => 'BackOffice'
                ],
                [
                    'nom' => 'Digital'
                ],
            ]
        );
    }
}
