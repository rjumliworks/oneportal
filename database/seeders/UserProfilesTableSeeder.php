<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserProfilesTableSeeder extends Seeder
{
    /**
     * Auto generated seeder file.
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('user_profiles')->delete();
        
        \DB::table('user_profiles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'lastname' => 'Jumli',
                'firstname' => 'eyJpdiI6IngvMDZpZnpXQTVRb0E3V0pndlNIUUE9PSIsInZhbHVlIjoiODlXWUlmWk9iS3V1SUF2MkJqZHNzZz09IiwibWFjIjoiOTkxYTU3OGM1ZDZhYWViMzhiNjI2NGEyNmI5NTYxYjhiYmQ0MTc3NjA3NjVjYmJjY2JmYjgwZTQwODk3MDY5MiIsInRhZyI6IiJ9',
                'middlename' => 'eyJpdiI6InUwTFYwcStkeXJiUWNpWTlWOUNuT1E9PSIsInZhbHVlIjoiOFlkbEhFdmsvTERJbnNJSG9DcnQ5Zz09IiwibWFjIjoiMGExMGJhYmIyMTQ1Y2U0NmE0MjAzODFjNzVmY2M0ODk5MDBlNTBkNTgxOWQxMmViNDcxOWY0OGY2YjcwMTYyNiIsInRhZyI6IiJ9',
                'mobile' => 'eyJpdiI6InE0TDI3UEYwNG83a1R1Vzl0VjcyRmc9PSIsInZhbHVlIjoiV0ZkcWN0WEk2L05jSmlQOHFadTRrQT09IiwibWFjIjoiZTI4MjgyMjFkOGE4NjdkODQwN2IxNWJhNWNkN2U2ZTQ0ODQ4NGIxNmJhNGY3MmU4NjI5Mjc3ZGRjYjFlOTlmZSIsInRhZyI6IiJ9',
                'birthdate' => 'eyJpdiI6Ikh6VmJXYitadUVST1NhNTc3OVprM3c9PSIsInZhbHVlIjoiSWxjTVYydzNDZW5CcWlIdVNreTNKUT09IiwibWFjIjoiMGZmNWY5YThlZTFlMmQwOGU4YzYwNzgxZTNlM2UzNjI0MGYxYTlhMmI4NjY2YzAyNDJlZmM3NDE4YTllZmE3OSIsInRhZyI6IiJ9',
                'birthmonth' => 3,
                'avatar' => 'profile-pictures/bOln665Q6mTNThtkrx5115CtQzQkAi8X1DdFSRv0.jpg',
                'signature' => 'nosig.jpg',
                'is_soloparent' => 0,
                'suffix_id' => NULL,
                'sex_id' => 166,
                'marital_id' => 3,
                'religion_id' => 20,
                'blood_id' => 7,
                'user_id' => 1,
                'created_at' => '2025-10-13 10:11:59',
                'updated_at' => '2025-10-14 16:36:35',
            ),
        ));

        
    }
}