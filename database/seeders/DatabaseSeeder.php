<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    public function run() {
        User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user'
        ]);

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('adminpassword'),
            'role' => 'admin'
        ]);

        Product::create(['name'=>'Wireless Mouse','sku'=>'WM-1001','price'=>499.00,'stock'=>50,'description'=>'Comfortable wireless mouse']);
        Product::create(['name'=>'Mechanical Keyboard','sku'=>'MK-2002','price'=>2499.00,'stock'=>30,'description'=>'RGB mechanical keyboard']);
        Product::create(['name'=>'USB-C Charger','sku'=>'UC-3003','price'=>999.00,'stock'=>100,'description'=>'Fast charging USB-C 30W']);
    }
}
