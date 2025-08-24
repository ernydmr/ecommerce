<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class InitialDataSeeder extends Seeder
{

    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@test.com'],
            ['name'  => 'Admin', 'password' => Hash::make('admin1234'), 'role' => 'admin'
            ]);

        $user = User::updateOrCreate(
            ['email' => 'user@test.com'],
            ['name'  => 'User', 'password' => Hash::make('user1234'), 'role' => 'user'
            ]);

        $catNames = ['Elektronik', 'Giyim', 'Kitap'];
        foreach($catNames as $name) {
            $category = Category::firstOrCreate(
                ['name' => $name],
                ['description' => $name.'açıklama']
            );
            Product::factory()
                ->count(5)
                ->for($category)
                ->create();
        }    
    }
}
