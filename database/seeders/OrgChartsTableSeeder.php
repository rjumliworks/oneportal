<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OrgChartsTableSeeder extends Seeder
{
    /**
     * Auto generated seeder file.
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('org_charts')->delete();
        
        \DB::table('org_charts')->insert(array (
            0 => 
            array (
                'id' => 1,
                'order' => 1,
                'designation_id' => 43,
                'assigned_id' => 2,
                'user_id' => NULL,
                'oic_id' => 13,
                'is_oic' => 1,
                'is_active' => 1,
                'created_at' => '2025-10-22 13:55:22',
                'updated_at' => '2025-10-22 13:55:22',
            ),
            1 => 
            array (
                'id' => 2,
                'order' => 2,
                'designation_id' => 44,
                'assigned_id' => 3,
                'user_id' => 10,
                'oic_id' => NULL,
                'is_oic' => 0,
                'is_active' => 1,
                'created_at' => '2025-10-22 13:55:22',
                'updated_at' => '2025-10-22 13:55:22',
            ),
            2 => 
            array (
                'id' => 3,
                'order' => 2,
                'designation_id' => 44,
                'assigned_id' => 4,
                'user_id' => 12,
                'oic_id' => NULL,
                'is_oic' => 0,
                'is_active' => 1,
                'created_at' => '2025-10-22 13:55:22',
                'updated_at' => '2025-10-22 13:55:22',
            ),
            3 => 
            array (
                'id' => 4,
                'order' => 3,
                'designation_id' => 45,
                'assigned_id' => 6,
                'user_id' => 13,
                'oic_id' => 16,
                'is_oic' => 1,
                'is_active' => 1,
                'created_at' => '2025-10-22 13:55:22',
                'updated_at' => '2025-10-22 13:55:22',
            ),
            4 => 
            array (
                'id' => 5,
                'order' => 3,
                'designation_id' => 45,
                'assigned_id' => 7,
                'user_id' => 14,
                'oic_id' => NULL,
                'is_oic' => 0,
                'is_active' => 1,
                'created_at' => '2025-10-22 13:55:22',
                'updated_at' => '2025-10-22 13:55:22',
            ),
            5 => 
            array (
                'id' => 6,
                'order' => 3,
                'designation_id' => 45,
                'assigned_id' => 8,
                'user_id' => 15,
                'oic_id' => NULL,
                'is_oic' => 0,
                'is_active' => 1,
                'created_at' => '2025-10-22 13:55:22',
                'updated_at' => '2025-10-22 13:55:22',
            ),
            6 => 
            array (
                'id' => 7,
                'order' => 3,
                'designation_id' => 46,
                'assigned_id' => 9,
                'user_id' => 12,
                'oic_id' => NULL,
                'is_oic' => 0,
                'is_active' => 1,
                'created_at' => '2025-10-22 13:55:22',
                'updated_at' => '2025-10-22 13:55:22',
            ),
        ));

        
    }
}