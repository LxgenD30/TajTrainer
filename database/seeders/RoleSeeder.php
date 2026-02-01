<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only insert if roles don't exist
        if (DB::table('role')->count() === 0) {
            DB::table('role')->insert([
                [
                    'id' => 2,
                    'user_type' => 'Student',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 3,
                    'user_type' => 'Teacher',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
