<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ListRolesTableSeeder extends Seeder
{
    /**
     * Auto generated seeder file.
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('list_roles')->delete();
        
        \DB::table('list_roles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Administrator',
                'type' => 'Staff',
                'is_active' => 1,
                'definition' => 'n/a',
                'created_at' => '2025-10-11 14:15:07',
                'updated_at' => '2025-10-11 14:15:07',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Employee',
                'type' => 'Staff',
                'is_active' => 1,
                'definition' => 'n/a',
                'created_at' => '2025-10-11 14:15:07',
                'updated_at' => '2025-10-11 14:15:07',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Hr Head',
                'type' => 'Staff',
                'is_active' => 1,
                'definition' => 'n/a',
                'created_at' => '2025-10-11 14:15:07',
                'updated_at' => '2025-10-11 14:15:07',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Hr Staff',
                'type' => 'Staff',
                'is_active' => 1,
                'definition' => 'n/a',
                'created_at' => '2025-10-11 14:15:07',
                'updated_at' => '2025-10-11 14:15:07',
            ),
        ));

        
    }
}