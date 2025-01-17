<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\ProductStock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $products = [
            [
                'name' => 'Blackbelt',
                'price' => 29.90,
                'image' => '1.png',
                'highlight' => true,
                'stocks' => [
                    'XS' => 2,
                    'S' => 2,
                    'M' => 2,
                    'L' => 2,
                    'XL' => 2,
                ],
            ],
            [
                'name' => 'BlueBelt',
                'price' => 29.90,
                'image' => '2.png',
                'highlight' => false,
                'stocks' => [
                    'XS' => 2,
                    'S' => 2,
                    'M' => 2,
                    'L' => 2,
                    'XL' => 2,
                ],
            ],
            [
                'name' => 'Street',
                'price' => 34.50,
                'image' => '3.png',
                'highlight' => false,
                'stocks' => [
                    'XS' => 2,
                    'S' => 2,
                    'M' => 2,
                    'L' => 2,
                    'XL' => 2,
                ],
            ],
            [
                'name' => 'Pokeball',
                'price' => 45.00,
                'image' => '4.png',
                'highlight' => true,
                'stocks' => [
                    'XS' => 2,
                    'S' => 2,
                    'M' => 2,
                    'L' => 2,
                    'XL' => 2,
                ],
            ],

            [
                'name' => 'PinkLady',
                'price' => 29.90,
                'image' => '5.png',
                'highlight' => true,
                'stocks' => [
                    'XS' => 2,
                    'S' => 2,
                    'M' => 2,
                    'L' => 2,
                    'XL' => 2,
                ],
            ],

            [
                'name' => 'Snow',
                'price' => 32.00,
                'image' => '6.png',
                'highlight' => true,
                'stocks' => [
                    'XS' => 2,
                    'S' => 2,
                    'M' => 2,
                    'L' => 2,
                    'XL' => 2,
                ],
            ],

            [
                'name' => 'Greyback',
                'price' => 28.50,
                'image' => '7.png',
                'highlight' => true,
                'stocks' => [
                    'XS' => 2,
                    'S' => 2,
                    'M' => 2,
                    'L' => 2,
                    'XL' => 2,
                ],
            ],

            [
                'name' => 'BLueCloud',
                'price' => 45.00,
                'image' => '8.png',
                'highlight' => true,
                'stocks' => [
                    'XS' => 2,
                    'S' => 2,
                    'M' => 2,
                    'L' => 2,
                    'XL' => 2,
                ],
            ],

            [
                'name' => 'BornInUsa',
                'price' => 59.90,
                'image' => '9.png',
                'highlight' => true,
                'stocks' => [
                    'XS' => 2,
                    'S' => 2,
                    'M' => 2,
                    'L' => 2,
                    'XL' => 2,
                ],
            ],

            [
                'name' => 'GreenSchool',
                'price' => 42.20,
                'image' => '10.png',
                'highlight' => true,
                'stocks' => [
                    'XS' => 2,
                    'S' => 2,
                    'M' => 2,
                    'L' => 2,
                    'XL' => 2,
                ],
            ],

        ];

        foreach ($products as $productData) {
            $product = new Product();
            $product->setName($productData['name']);
            $product->setPrice($productData['price']);
            $product->setImage($productData['image']);

            foreach ($productData['stocks'] as $size => $quantity) {
                $productStock = new ProductStock();
                $productStock->setSize($size);
                $productStock->setQuantity($quantity);
                $productStock->setProduct($product);

                $manager->persist($productStock);
            }

            $manager->persist($product);
        }

        $manager->flush();
    }
}
