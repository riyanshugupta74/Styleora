<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Users
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@styleora.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        
        User::factory()->create([
            'name' => 'Test Customer',
            'email' => 'customer@styleora.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        // 2. Categories & Subcategories
        $categories = [
            'Men' => ['T-Shirts', 'Shirts', 'Jeans', 'Trousers', 'Jackets', 'Hoodies', 'Shoes', 'Accessories'],
            'Women' => ['Tops', 'Dresses', 'Jeans', 'Trousers', 'Sarees', 'Kurtas', 'Shoes', 'Bags', 'Accessories']
        ];

        $categoryMap = [];
        $subcatMap = [];

        foreach ($categories as $catName => $subcats) {
            $category = Category::create([
                'name' => $catName,
                'slug' => Str::slug($catName),
                'status' => 1
            ]);
            $categoryMap[$catName] = $category->id;

            foreach ($subcats as $subName) {
                $subcat = Subcategory::create([
                    'category_id' => $category->id,
                    'name' => $subName,
                    'slug' => Str::slug($category->name . ' ' . $subName),
                    'status' => 1
                ]);
                $subcatMap[$catName . '-' . $subName] = $subcat->id;
            }
        }

        // 3. Brands
        $brands = ['Roadster', 'Puma', 'Nike', 'H&M', 'Zara', 'Biba', 'W', 'Levis', 'Allen Solly', 'Highlander'];
        $brandMap = [];
        foreach ($brands as $brandName) {
            $brand = Brand::create([
                'name' => $brandName,
                'slug' => Str::slug($brandName),
                'status' => 1
            ]);
            $brandMap[$brandName] = $brand->id;
        }

        // 4. Colors & Sizes
        $colors = [
            'Black' => '#000000', 'White' => '#FFFFFF', 'Red' => '#EF4444', 
            'Blue' => '#3B82F6', 'Navy' => '#1E3A8A', 'Olive' => '#4D7C0F',
            'Grey' => '#6B7280', 'Pink' => '#EC4899', 'Yellow' => '#EAB308'
        ];
        $colorMap = [];
        foreach ($colors as $name => $hex) {
            $color = Color::create(['name' => $name, 'hex_code' => $hex]);
            $colorMap[$name] = $color->id;
        }

        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
        $sizeMap = [];
        foreach ($sizes as $index => $size) {
            $sz = Size::create(['name' => $size, 'display_order' => $index]);
            $sizeMap[$size] = $sz->id;
        }

        // 5. Curated Products (20 Men, 20 Women)
        $catalog = [
            // MEN (20)
            ['Men', 'T-Shirts', 'Roadster', 'Solid Crew Neck Cotton T-shirt', 999, 499, 'Black', 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Men', 'T-Shirts', 'Puma', 'Graphic Print Training T-shirt', 1499, 899, 'White', 'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Men', 'T-Shirts', 'Nike', 'Dri-FIT Active Running Top', 2495, 1995, 'Blue', 'https://images.unsplash.com/photo-1581655353564-df123a1eb820?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Men', 'T-Shirts', 'Highlander', 'Slim Fit Striped Casual T-shirt', 1199, 599, 'Navy', 'https://images.unsplash.com/photo-1576566588028-4147f3842f27?auto=format&fit=crop&q=80&w=600&h=800'],
            
            ['Men', 'Shirts', 'Allen Solly', 'Men Slim Fit Checked Casual Shirt', 1899, 949, 'Blue', 'https://images.unsplash.com/photo-1596755094514-f87e32f6b717?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Men', 'Shirts', 'H&M', 'Regular Fit Linen-blend Shirt', 2299, null, 'White', 'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Men', 'Shirts', 'Zara', 'Textured Button-Down Collar Shirt', 3590, 2990, 'Black', 'https://images.unsplash.com/photo-1598033129183-c4f50c736f10?auto=format&fit=crop&q=80&w=600&h=800'],
            
            ['Men', 'Jeans', 'Levis', 'Men 511 Slim Fit Stretchable Jeans', 3299, 1979, 'Blue', 'https://images.unsplash.com/photo-1542272604-780c968509e4?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Men', 'Jeans', 'Roadster', 'Men Skinny Fit Mid-Rise Clean Look Jeans', 1999, 899, 'Black', 'https://images.unsplash.com/photo-1604198453316-431e7f67be64?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Men', 'Trousers', 'Allen Solly', 'Men Tapered Fit Chinos', 2499, 1249, 'Olive', 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Men', 'Trousers', 'Highlander', 'Men Slim Fit Track Pants', 1499, 699, 'Grey', 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?auto=format&fit=crop&q=80&w=600&h=800'],
            
            ['Men', 'Jackets', 'Puma', 'Men Water-Repellent Puffer Jacket', 4999, 2499, 'Navy', 'https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Men', 'Jackets', 'Roadster', 'Men Solid Tailored Jacket', 3999, 1599, 'Olive', 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Men', 'Hoodies', 'H&M', 'Relaxed Fit Zip-through Hoodie', 2299, null, 'Grey', 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Men', 'Hoodies', 'Zara', 'Basic Pullover Hoodie', 3590, 1990, 'Black', 'https://images.unsplash.com/photo-1509942774463-acf339cf87d5?auto=format&fit=crop&q=80&w=600&h=800'],
            
            ['Men', 'Shoes', 'Nike', 'Air Max Pulse Running Shoes', 13995, null, 'White', 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Men', 'Shoes', 'Puma', 'Smash v2 Sneakers', 3999, 1999, 'Black', 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Men', 'Shoes', 'Roadster', 'Men Casual White Sneakers', 1999, 899, 'White', 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?auto=format&fit=crop&q=80&w=600&h=800'],
            
            ['Men', 'Accessories', 'Levis', 'Men Textured Leather Belt', 1299, 779, 'Black', 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Men', 'Accessories', 'H&M', 'Men Polarized Sunglasses', 1499, null, 'Black', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?auto=format&fit=crop&q=80&w=600&h=800'],

            // WOMEN (20)
            ['Women', 'Tops', 'Zara', 'Asymmetric Crop Top', 1890, 1290, 'Black', 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Women', 'Tops', 'H&M', 'Rib-knit V-neck Top', 1499, null, 'White', 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Women', 'Tops', 'Roadster', 'Women Floral Print Peplum Top', 1299, 599, 'Pink', 'https://images.unsplash.com/photo-1588117305388-c2631a279f82?auto=format&fit=crop&q=80&w=600&h=800'],
            
            ['Women', 'Dresses', 'Zara', 'Satin Slip Dress', 3990, 2990, 'Navy', 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Women', 'Dresses', 'H&M', 'Floral Maxi Dress', 2999, 1499, 'Pink', 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Women', 'Dresses', 'Biba', 'A-Line Cotton Dress', 2499, 1249, 'Yellow', 'https://images.unsplash.com/photo-1515347619362-e6fd4e9541ea?auto=format&fit=crop&q=80&w=600&h=800'],
            
            ['Women', 'Jeans', 'Levis', '711 Skinny Women Jeans', 3599, 2159, 'Blue', 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Women', 'Jeans', 'H&M', 'Wide High Jeans', 2299, 1149, 'Blue', 'https://images.unsplash.com/photo-1582552938357-32b906df40cb?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Women', 'Trousers', 'Zara', 'High-Waist Tailored Trousers', 3590, null, 'Black', 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Women', 'Trousers', 'Roadster', 'Women Wide Leg Trousers', 1799, 899, 'Olive', 'https://images.unsplash.com/photo-1509631179647-0c1157a8bf60?auto=format&fit=crop&q=80&w=600&h=800'],
            
            ['Women', 'Sarees', 'Biba', 'Women Pink & Gold-Toned Silk Saree', 5999, 2999, 'Pink', 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Women', 'Sarees', 'W', 'Women Blue Printed Saree', 3999, 1999, 'Blue', 'https://images.unsplash.com/photo-1583391733958-6934ee8b1912?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Women', 'Kurtas', 'Biba', 'Women Yellow Straight Kurta', 1999, 999, 'Yellow', 'https://images.unsplash.com/photo-1583391265517-35bbd323fbce?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Women', 'Kurtas', 'W', 'Women White Embroidered A-Line Kurta', 2499, 1249, 'White', 'https://images.unsplash.com/photo-1603344797033-f0f4f587ab60?auto=format&fit=crop&q=80&w=600&h=800'],
            
            ['Women', 'Shoes', 'Nike', 'Women Revolution 6 Running Shoes', 3695, 2955, 'Black', 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Women', 'Shoes', 'Puma', 'Women Carina L Sneakers', 4499, 2249, 'White', 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Women', 'Shoes', 'Zara', 'Leather Heeled Sandals', 4990, null, 'Black', 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?auto=format&fit=crop&q=80&w=600&h=800'],
            
            ['Women', 'Bags', 'H&M', 'Quilted Shoulder Bag', 2299, 1149, 'Black', 'https://images.unsplash.com/photo-1584916201218-f4242ceb4809?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Women', 'Bags', 'Zara', 'Minimalist Crossbody Bag', 2990, null, 'White', 'https://images.unsplash.com/photo-1591561954557-26941169b49e?auto=format&fit=crop&q=80&w=600&h=800'],
            ['Women', 'Accessories', 'Puma', 'Women Core Base Cap', 999, 499, 'Pink', 'https://images.unsplash.com/photo-1521369909029-2afed882ba54?auto=format&fit=crop&q=80&w=600&h=800'],
        ];

        foreach ($catalog as $index => $item) {
            $cat = $item[0];
            $subcat = $item[1];
            $brand = $item[2];
            $name = $item[3];
            $price = $item[4];
            $discount = $item[5];
            $color = $item[6];
            $image = $item[7];

            $isNew = (bool)rand(0, 1);
            $isTrending = (bool)rand(0, 1);

            $product = Product::create([
                'name' => $name,
                'slug' => Str::slug($name) . '-' . Str::random(4),
                'sku' => strtoupper(Str::random(8)),
                'description' => 'Premium ' . $name . ' by ' . $brand . '. Guaranteed quality and comfort. Elevate your fashion with our latest collection.',
                'category_id' => $categoryMap[$cat],
                'subcategory_id' => $subcatMap[$cat . '-' . $subcat],
                'brand_id' => $brandMap[$brand],
                'price' => $price,
                'discount_price' => $discount,
                'stock' => rand(50, 200),
                'status' => 1,
                'is_featured' => 1,
                'is_trending' => $isTrending,
                'is_new_arrival' => $isNew
            ]);

            // Add Image
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $image,
                'sort_order' => 0,
                'is_primary' => 1
            ]);

            // Add Variants (Sizes)
            foreach ($sizes as $sizeName) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'color_id' => $colorMap[$color],
                    'size_id' => $sizeMap[$sizeName],
                    'sku' => $product->sku . '-' . strtoupper(substr($color,0,3)) . '-' . $sizeName,
                    'price' => $price,
                    'stock' => rand(5, 50),
                    'image' => $image
                ]);
            }
        }
    }
}
