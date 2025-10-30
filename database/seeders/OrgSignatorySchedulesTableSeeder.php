<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OrgSignatorySchedulesTableSeeder extends Seeder
{
    /**
     * Auto generated seeder file.
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('org_signatory_schedules')->delete();
        
        \DB::table('org_signatory_schedules')->insert(array (
            0 => 
            array (
                'id' => 5,
                'start_at' => '2025-01-01',
                'end_at' => '2025-12-31',
                'signatory_id' => 2,
                'user_id' => 10,
                'is_designated' => 1,
                'is_ongoing' => 1,
                'is_completed' => 0,
                'created_at' => '2025-10-30 10:11:22',
                'updated_at' => '2025-10-30 10:11:22',
            ),
            1 => 
            array (
                'id' => 6,
                'start_at' => '2025-01-01',
                'end_at' => '2025-12-31',
                'signatory_id' => 3,
                'user_id' => 12,
                'is_designated' => 1,
                'is_ongoing' => 1,
                'is_completed' => 0,
                'created_at' => '2025-10-30 10:11:22',
                'updated_at' => '2025-10-30 10:11:22',
            ),
            2 => 
            array (
                'id' => 7,
                'start_at' => '2025-01-01',
                'end_at' => '2025-12-31',
                'signatory_id' => 4,
                'user_id' => 13,
                'is_designated' => 1,
                'is_ongoing' => 1,
                'is_completed' => 0,
                'created_at' => '2025-10-30 10:11:22',
                'updated_at' => '2025-10-30 10:11:22',
            ),
            3 => 
            array (
                'id' => 8,
                'start_at' => '2025-01-01',
                'end_at' => '2025-12-31',
                'signatory_id' => 5,
                'user_id' => 14,
                'is_designated' => 1,
                'is_ongoing' => 1,
                'is_completed' => 0,
                'created_at' => '2025-10-30 10:11:22',
                'updated_at' => '2025-10-30 10:11:22',
            ),
            4 => 
            array (
                'id' => 9,
                'start_at' => '2025-01-01',
                'end_at' => '2025-12-31',
                'signatory_id' => 6,
                'user_id' => 15,
                'is_designated' => 1,
                'is_ongoing' => 1,
                'is_completed' => 0,
                'created_at' => '2025-10-30 10:11:22',
                'updated_at' => '2025-10-30 10:11:22',
            ),
            5 => 
            array (
                'id' => 10,
                'start_at' => '2025-01-01',
                'end_at' => '2025-12-31',
                'signatory_id' => 7,
                'user_id' => 12,
                'is_designated' => 1,
                'is_ongoing' => 1,
                'is_completed' => 0,
                'created_at' => '2025-10-30 10:11:22',
                'updated_at' => '2025-10-30 10:11:22',
            ),
        ));

        
    }
}