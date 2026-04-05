<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::where('role', '>', 0)->first()?->id ?? 1;

        $products = [
            [
                'user_id'     => $adminId,
                'name'        => 'Клавиатура Mechanical RGB',
                'description' => 'Крутая механика для геймеров с подсветкой.',
                'price'       => 120,
                'stock'       => 15,
                'image'       => 'https://cdn.mos.cms.futurecdn.net/hqd9fFRUKsZHgWckKKZtTR.jpg',
                'is_active'   => true,
            ],
            [
                'user_id'     => $adminId,
                'name'        => 'Игровая мышь Wireless',
                'description' => 'Беспроводная мышь с сенсором 16000 DPI.',
                'price'       => 250,
                'stock'       => 25,
                'image'       => 'https://i.insider.com/652984596561dd877e7a5b61?width=700',
                'is_active'   => true,
            ],
            [
                'user_id'     => $adminId,
                'name'        => 'Монитор 27" 144Hz',
                'description' => 'Full HD монитор с IPS матрицей.',
                'price'       => 550,
                'stock'       => 5,
                'image'       => 'https://i.rtings.com/assets/pages/v9xQhvdG/best-27-inch-gaming-monitors-20251126-medium.jpg?format=auto',
                'is_active'   => true,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['name' => $product['name']],
                $product
            );
        }
    }
}
