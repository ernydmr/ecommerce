<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            InitialDataSeeder::class,
        ]);
    }
}
