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
            1 => 
            array (
                'id' => 2,
                'name' => 'Active',
                'classification' => 'Status',
                'type' => 'n/a',
                'color' => 'text-white',
                'bg' => 'bg-success',
                'icon' => 'n/a',
                'is_active' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Retired',
                'classification' => 'Status',
                'type' => 'n/a',
                'color' => 'text-white',
                'bg' => 'bg-info',
                'icon' => 'n/a',
                'is_active' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Resigned',
                'classification' => 'Status',
                'type' => 'n/a',
                'color' => 'text-white',
                'bg' => 'bg-warning',
                'icon' => 'n/a',
                'is_active' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Terminated',
                'classification' => 'Status',
                'type' => 'n/a',
                'color' => 'text-white',
                'bg' => 'bg-danger',
                'icon' => 'n/a',
                'is_active' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Dismissed',
                'classification' => 'Status',
                'type' => 'n/a',
                'color' => 'text-white',
                'bg' => 'bg-danger',
                'icon' => 'n/a',
                'is_active' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'End of Contract',
                'classification' => 'Status',
                'type' => 'n/a',
                'color' => 'text-white',
                'bg' => 'bg-dark',
                'icon' => 'n/a',
                'is_active' => 1,
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Deceased',
                'classification' => 'Status',
                'type' => 'n/a',
                'color' => 'text-white',
                'bg' => 'bg-dark',
                'icon' => 'n/a',
                'is_active' => 1,
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Absconded',
                'classification' => 'Status',
                'type' => 'n/a',
                'color' => 'text-white',
                'bg' => 'bg-warning',
                'icon' => 'n/a',
                'is_active' => 1,
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Active',
                'classification' => 'Contract',
                'type' => 'n/a',
                'color' => 'text-white',
                'bg' => 'bg-warning',
                'icon' => 'n/a',
                'is_active' => 1,
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'Promoted',
                'classification' => 'Contract',
                'type' => 'n/a',
                'color' => 'text-white',
                'bg' => 'bg-warning',
                'icon' => 'n/a',
                'is_active' => 1,
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'Salary Increased',
                'classification' => 'Contract',
                'type' => 'n/a',
                'color' => 'text-white',
                'bg' => 'bg-warning',
                'icon' => 'n/a',
                'is_active' => 1,
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'Terminated',
                'classification' => 'Contract',
                'type' => 'n/a',
                'color' => 'text-white',
                'bg' => 'bg-warning',
                'icon' => 'n/a',
                'is_active' => 1,
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'Resigned',
                'classification' => 'Contract',
                'type' => 'n/a',
                'color' => 'text-white',
                'bg' => 'bg-warning',
                'icon' => 'n/a',
                'is_active' => 1,
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'Absorbed',
                'classification' => 'Contract',
                'type' => 'n/a',
                'color' => 'text-white',
                'bg' => 'bg-warning',
                'icon' => 'n/a',
                'is_active' => 1,
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'Ended',
                'classification' => 'Contract',
                'type' => 'n/a',
                'color' => 'text-white',
                'bg' => 'bg-warning',
                'icon' => 'n/a',
                'is_active' => 1,
            ),
        ));

        
    }
}