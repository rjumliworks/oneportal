<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Auto generated seeder file.
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'username' => 'rij0311',
                'email' => 'eyJpdiI6InVHMFVHbWw3bUVSOGFzdGpDTGtNa0E9PSIsInZhbHVlIjoiYnc5YXRiREdNWHlnVGQzTDlPaE5PQkxJTXZaNTdvSEVQWTdvaW9tcHcxcz0iLCJtYWMiOiIyYzg1NWQ2MjZlZTJiYWYwMjc1MmI5Y2M1MWY5ZDI5MDFjNDc4Y2ExNGEyZjM1NjZmYjEyNzAzZmQ5YThmOGFiIiwidGFnIjoiIn0=',
                'kradworkz' => '943317e3d4eb45f8444d66b2206cdd99241a95868889d194d511d8346056ac39',
                'password' => '$2y$12$s1CjKf.NYAbWx5Rd2QUy1OAUVV96laMOtwnsmJQc5dkP50mBPwh5a',
                'is_active' => 1,
                'must_change' => 0,
                'two_factor_secret' => NULL,
                'two_factor_recovery_codes' => NULL,
                'remember_token' => NULL,
                'email_verified_at' => '2024-05-15 08:46:33',
                'password_changed_at' => '2025-10-13 13:19:09',
                'two_factor_confirmed_at' => NULL,
                'created_at' => '2025-10-13 10:11:59',
                'updated_at' => '2025-10-14 16:36:35',
            ),
        ));

        
    }
}