<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserOrganizationsTableSeeder extends Seeder
{
    /**
     * Auto generated seeder file.
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('user_organizations')->delete();
        
        \DB::table('user_organizations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'status_id' => 1,
                'type_id' => 16,
                'position_id' => 12,
                'salary_id' => 31,
                'division_id' => 4,
                'unit_id' => 15,
                'station_id' => 5,
                'user_id' => 1,
                'created_at' => '2025-10-14 14:40:03',
                'updated_at' => '2025-10-14 16:36:35',
            ),
        ));

        
    }
}