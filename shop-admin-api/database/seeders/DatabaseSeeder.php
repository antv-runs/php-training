<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create default admin
        if (!User::where('email', 'uter.vanan@gmail.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'uter.vanan@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);
        }

        $this->createUsers();
        $this->createCategories();
        $this->createProducts();
        $this->createOrders();
    }

    public function createUsers()
    {
        for ($i = 1; $i <= 20; $i++) {
            $email = "user{$i}@example.com";

            if (!User::where('email', $email)->exists()) {
                User::create([
                    'name' => "User {$i}",
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => 'user',
                ]);
            }
        }
    }

    public function createCategories()
    {
        $categories = [
            'Áo Thun Nam',
            'Áo Thun Nữ',
            'Áo Sơ Mi Nam',
            'Áo Sơ Mi Nữ',
            'Quần Jeans Nam',
            'Quần Jeans Nữ',
            'Quần Short',
            'Váy Đầm',
            'Áo Khoác',
            'Áo Hoodie',
            'Áo Blazer',
            'Quần Tây',
            'Đồ Thể Thao',
            'Đồ Ngủ',
            'Đồ Lót',
            'Áo Len',
            'Áo Polo',
            'Set Bộ',
            'Đồ Công Sở',
            'Phụ Kiện Thời Trang'
        ];

        foreach ($categories as $categoryName) {
            Category::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                [
                    'name' => $categoryName,
                    'description' => "Danh mục {$categoryName}"
                ]
            );
        }
    }

    public function createProducts()
    {
        $products = [
            'Áo Thun Nam Basic Cotton',
            'Áo Thun Nữ Form Rộng',
            'Áo Sơ Mi Nam Trắng Công Sở',
            'Áo Sơ Mi Nữ Tay Dài Hàn Quốc',
            'Quần Jeans Nam Slim Fit',
            'Quần Jeans Nữ Ống Rộng',
            'Quần Short Thể Thao Nam',
            'Váy Đầm Dự Tiệc Sang Trọng',
            'Áo Khoác Jean Unisex',
            'Áo Hoodie Oversize',
            'Áo Blazer Nữ Thanh Lịch',
            'Quần Tây Nam Cao Cấp',
            'Bộ Đồ Thể Thao Nữ',
            'Bộ Đồ Ngủ Lụa',
            'Set Đồ Lót Cotton',
            'Áo Len Cổ Lọ Mùa Đông',
            'Áo Polo Nam Cao Cấp',
            'Set Bộ Nữ Thời Trang',
            'Đầm Công Sở Thanh Lịch',
            'Thắt Lưng Da Nam'
        ];

        $allCategories = Category::all();

        foreach ($products as $productName) {
            $slug = Str::slug($productName);

            Product::firstOrCreate(
                ['name' => $productName],
                [
                    'slug' => $slug,
                    'price' => rand(100000, 1000000),
                    'description' => "Sản phẩm {$productName} chất lượng cao, thời trang hiện đại.",
                    'category_id' => $allCategories->random()->id,
                    'image' => 'default-product.jpg'
                ]
            );
        }
    }

    public function createOrders()
    {
        $targetEmail = 'user1@example.com';
        $user = User::where('email', $targetEmail)->first();

        if (!$user) {
            return;
        }

        $products = Product::all();

        for ($i = 0; $i < 20; $i++) {

            DB::transaction(function () use ($user, $products) {

                $order = Order::create([
                    'user_id' => $user->id,
                    'total_amount' => 0,
                    'status' => collect(['pending', 'processing', 'completed'])->random()
                ]);

                $totalAmount = 0;

                $itemsCount = rand(1, 5);
                $selectedProducts = $products->random($itemsCount);

                foreach ($selectedProducts as $product) {

                    $quantity = rand(1, 3);
                    $price = $product->price;
                    $total = $price * $quantity;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $price,
                        'total' => $total,
                    ]);

                    $totalAmount += $total;
                }

                $order->update([
                    'total_amount' => $totalAmount
                ]);
            });
        }
    }
}
