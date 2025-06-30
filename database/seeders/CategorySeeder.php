<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Categories\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Transportlīdzekļi',
                'slug' => 'transport',
                'description' => 'Automašīnas, motocikli, velosipēdi',
                'icon' => 'car',
                'sort_order' => 1,
                'children' => [
                    'Automašīnas',
                    'Motocikli',
                    'Velosipēdi',
                    'Kravas automašīnas',
                    'Rezerves daļas',
                ]
            ],
            [
                'name' => 'Nekustamais īpašums',
                'slug' => 'real-estate',
                'description' => 'Dzīvokļi, mājas, zeme',
                'icon' => 'home',
                'sort_order' => 2,
                'children' => [
                    'Dzīvokļi',
                    'Mājas',
                    'Zeme',
                    'Komercobjekti',
                    'Garāžas',
                ]
            ],
            [
                'name' => 'Darbs',
                'slug' => 'jobs',
                'description' => 'Vakances un darba meklējumi',
                'icon' => 'briefcase',
                'sort_order' => 3,
                'children' => [
                    'Pilna laika darbs',
                    'Nepilna laika darbs',
                    'Sezonāls darbs',
                    'Prakses vietas',
                ]
            ],
            [
                'name' => 'Elektronika',
                'slug' => 'electronics',
                'description' => 'Telefoni, datori, tehnika',
                'icon' => 'smartphone',
                'sort_order' => 4,
                'children' => [
                    'Telefoni',
                    'Datori',
                    'TV un audio',
                    'Foto tehnika',
                    'Spēļu konsoles',
                ]
            ],
            [
                'name' => 'Mājas un dārzs',
                'slug' => 'home-garden',
                'description' => 'Mēbeles, dārza tehnika, būvmateriāli',
                'icon' => 'home',
                'sort_order' => 5,
                'children' => [
                    'Mēbeles',
                    'Dārza tehnika',
                    'Būvmateriāli',
                    'Instrumenti',
                    'Dekori',
                ]
            ],
            [
                'name' => 'Apģērbs un aksesuāri',
                'slug' => 'fashion',
                'description' => 'Apģērbi, apavi, aksesuāri',
                'icon' => 'shirt',
                'sort_order' => 6,
                'children' => [
                    'Sieviešu apģērbs',
                    'Vīriešu apģērbs',
                    'Bērnu apģērbs',
                    'Apavi',
                    'Aksesuāri',
                ]
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $parent = Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );

            foreach ($children as $childName) {
                Category::firstOrCreate(
                    [
                        'name' => $childName,
                        'parent_id' => $parent->id,
                    ],
                    [
                        'name' => $childName,
                        'slug' => Str::slug($childName),
                        'parent_id' => $parent->id,
                        'is_active' => true,
                        'sort_order' => 0,
                    ]
                );
            }
        }
    }
}
