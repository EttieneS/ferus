<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        $this->call([
            SlaSeeder::class,
            QueueSeeder::class,
            UserSeeder::class,
            UserRoleSeeder::class,
            CustomerSeeder::class,
            TicketSeeder::class,
            MailRecipientSeeder::class,
        ]);
    }
}
