<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::factory()->create([
            'nomRole' => 'ADMIN',
        ]);

        Role::factory()->create([
            'nomRole' => 'CLIENT',
        ]);

        Role::factory()->create([
            'nomRole' => 'DISTRIBUTEUR',
        ]);
    }
}
