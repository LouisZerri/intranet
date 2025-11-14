<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            //UserSeeder::class,
            //NewsSeeder::class,
            //MissionSeeder::class,
            //CommunicationProductSeeder::class,
            //FormationsSeeder::class,
            //ContactSeeder::class,
            //FaqSeeder::class,
            //ResourceSeeder::class,
            //FormationFileSeeder::class,
            //CommercialModuleSeeder::class,
            PredefinedServiceSeeder::class
        ]);
    }
}