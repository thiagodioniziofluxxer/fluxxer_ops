<?php

namespace Database\Seeders;

use App\Models\Host;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HostTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Host::create([
            'ip'=>'177.136.251.75',
            'credentials'=>json_encode(['username'=>'admin','password'=>'admin123'])
        ]);
    }
}
