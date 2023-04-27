<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MotifAbsencesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('motif_absences')->truncate();

        DB::table('motif_absences')->insert(
            [
                [
                    'nom' => 'Permission'
                ],
                [
                    'nom' => 'Absence non justifié'
                ],
                [
                    'nom' => 'Absence justifié'
                ],
                [
                    'nom' => 'Digital'
                ],
                [
                    'nom' => 'Absence pour sanction'
                ],
            ]
        );
    }
}
