<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SignatoriesTableSeeder extends Seeder
{
    /**
     * Auto generated seeder file.
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('signatories')->delete();
        
        \DB::table('signatories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'designationable_id' => 1,
                'designationable_type' => 'App\\Models\\OrganizationalChart',
                'user_id' => NULL,
                'oic_id' => 13,
                'is_oic' => 1,
                'is_topmanagement' => 1,
                'is_active' => 1,
                'created_at' => '2025-10-23 09:02:10',
                'updated_at' => '2025-10-23 09:02:10',
            ),
            1 => 
            array (
                'id' => 2,
                'designationable_id' => 2,
                'designationable_type' => 'App\\Models\\OrganizationalChart',
                'user_id' => 10,
                'oic_id' => NULL,
                'is_oic' => 0,
                'is_topmanagement' => 1,
                'is_active' => 1,
                'created_at' => '2025-10-23 09:02:10',
                'updated_at' => '2025-10-23 09:02:10',
            ),
            2 => 
            array (
                'id' => 3,
                'designationable_id' => 3,
                'designationable_type' => 'App\\Models\\OrganizationalChart',
                'user_id' => 12,
                'oic_id' => NULL,
                'is_oic' => 0,
                'is_topmanagement' => 1,
                'is_active' => 1,
                'created_at' => '2025-10-23 09:02:10',
                'updated_at' => '2025-10-23 09:02:10',
            ),
            3 => 
            array (
                'id' => 4,
                'designationable_id' => 4,
                'designationable_type' => 'App\\Models\\OrganizationalChart',
                'user_id' => 13,
                'oic_id' => 16,
                'is_oic' => 1,
                'is_topmanagement' => 1,
                'is_active' => 1,
                'created_at' => '2025-10-23 09:02:10',
                'updated_at' => '2025-10-23 09:02:10',
            ),
            4 => 
            array (
                'id' => 5,
                'designationable_id' => 5,
                'designationable_type' => 'App\\Models\\OrganizationalChart',
                'user_id' => 14,
                'oic_id' => NULL,
                'is_oic' => 0,
                'is_topmanagement' => 1,
                'is_active' => 1,
                'created_at' => '2025-10-23 09:02:10',
                'updated_at' => '2025-10-23 09:02:10',
            ),
            5 => 
            array (
                'id' => 6,
                'designationable_id' => 6,
                'designationable_type' => 'App\\Models\\OrganizationalChart',
                'user_id' => 15,
                'oic_id' => NULL,
                'is_oic' => 0,
                'is_topmanagement' => 1,
                'is_active' => 1,
                'created_at' => '2025-10-23 09:02:10',
                'updated_at' => '2025-10-23 09:02:10',
            ),
            6 => 
            array (
                'id' => 7,
                'designationable_id' => 7,
                'designationable_type' => 'App\\Models\\OrganizationalChart',
                'user_id' => 12,
                'oic_id' => NULL,
                'is_oic' => 0,
                'is_topmanagement' => 1,
                'is_active' => 1,
                'created_at' => '2025-10-23 09:02:10',
                'updated_at' => '2025-10-23 09:02:10',
            ),
        ));

        
    }
}