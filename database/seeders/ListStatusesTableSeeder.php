<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ListStatusesTableSeeder extends Seeder
{
    /**
     * Auto generated seeder file.
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('list_statuses')->delete();
        
        \DB::table('list_statuses')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Not Available',
                'classification' => 'n/a',
                'type' => 'n/a',
                'color' => 'n/a',
                'bg' => 'n/a',
                'icon' => 'n/a',
                'is_active' => 1,
            ),
        ));

        
    }
}