<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder {
    public function run(): void {
        Customer::firstOrCreate(
            ['email' => 'someone@example.com'],
            ['full_name' => 'Someone Example', 'phone' => '1234567890', 'address' => '123 Some Street']
        );

        Customer::firstOrCreate(
            ['email' => 'boss@example.com'],
            ['full_name' => 'Boss Example', 'phone' => '2345678901', 'address' => '456 Boss Avenue']
        );

        Customer::firstOrCreate(
            ['email' => 'manager@example.com'],
            ['full_name' => 'Manager Example', 'phone' => '3456789012', 'address' => '789 Manager Blvd']
        );

        echo "✔ Customers seeded.\n";
    }
}
