<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ClientTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Koch
        //Veneza
        //Kikker
        //Dalben
        //Mercale
        //Heineken Tenda
        //Demo
        //Ivasko
        //Danny Cosmeticos
        //Heineken Hiper Ideal
        //Demo Simples
        //Araujo
        //Tim log
        //Rede Pas

        $clients = [
            ['name' => 'Koch', 'status' => 'active'],
            ['name' => 'Veneza', 'status' => 'active'],
            ['name' => 'Kikker', 'status' => 'active'],
            ['name' => 'Dalben', 'status' => 'active'],
            ['name' => 'Mercale', 'status' => 'active'],
            ['name' => 'Heineken Tenda', 'status' => 'active'],
            ['name' => 'Demo', 'status' => 'active'],
            ['name' => 'Ivasko', 'status' => 'active'],
            ['name' => 'Danny Cosmeticos', 'status' => 'active'],
            ['name' => 'Heineken Hiper Ideal', 'status' => 'active'],
            ['name' => 'Araujo', 'status' => 'active'],
            ['name' => 'Tim log', 'status' => 'active'],
            ['name' => 'Rede Pas', 'status' => 'active'],
        ];
        foreach ($clients as $client) {
            $client = \App\Models\Client::create($client);
            $client->clientConfig()->create([
                'host_id' => \App\Models\Host::first()->id,
                'config_key' => 'portinair-key',
                'db_host' => \App\Models\Host::first()->ip,
                'db_port' => '5432',
                'db_username' => 'user',
                'db_password' => 'admin',
            ]);
        }

    }
}
