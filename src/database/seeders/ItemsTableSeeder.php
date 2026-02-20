<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        if ($users->count() < 3) {
            $users = collect([
                User::factory()->create([
                    'name' => 'User1',
                    'email' => 'user1@example.com',
                    'password' => Hash::make('password'),
                ]),
                User::factory()->create([
                    'name' => 'User2',
                    'email' => 'user2@example.com',
                    'password' => Hash::make('password'),
                ]),
                User::factory()->create([
                    'name' => 'User3',
                    'email' => 'user3@example.com',
                    'password' => Hash::make('password'),
                ]),
            ]);
        }

        $itemsdata = [
            [
                'name'=>'腕時計',
                'price'=>15000,
                'brand'=>'Rolax',
                'description'=>'スタイリッシュなデザインのメンズ時計',
                'image_path'=>'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
                'condition'=>'良好',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'name'=>'HDD',
                'price'=>5000,
                'brand'=>'西芝',
                'description'=>'高速で信頼性の高いハードディスク',
                'image_path'=>'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
                'condition'=>'目立った傷や汚れなし',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'name'=>'玉ねぎ3束',
                'price'=>300,
                'brand'=>null,
                'description'=>'新鮮な玉ねぎ3束のセット',
                'image_path'=>'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
                'condition'=>'やや傷や汚れあり',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'name'=>'革靴',
                'price'=>4000,
                'brand'=>null,
                'description'=>'クラシックなデザインの革靴',
                'image_path'=>'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
                'condition'=>'状態が悪い',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'name'=>'ノートPC',
                'price'=>45000,
                'brand'=>null,
                'description'=>'高性能なノートパソコン',
                'image_path'=>'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
                'condition'=>'良好',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'name'=>'マイク',
                'price'=>8000,
                'brand'=>null,
                'description'=>'高音質のレコーディング用マイク',
                'image_path'=>'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
                'condition'=>'目立った傷や汚れなし',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'name'=>'ショルダーバッグ',
                'price'=>3500,
                'brand'=>null,
                'description'=>'おしゃれなショルダーバッグ',
                'image_path'=>'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
                'condition'=>'やや傷や汚れあり',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'name'=>'タンブラー',
                'price'=>500,
                'brand'=>null,
                'description'=>'使いやすいタンブラー',
                'image_path'=>'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
                'condition'=>'状態が悪い',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'name'=>'コーヒーミル',
                'price'=>4000,
                'brand'=>'starbacks',
                'description'=>'手動のコーヒーミル',
                'image_path'=>'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
                'condition'=>'良好',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'name'=>'メイクセット',
                'price'=>2500,
                'brand'=>null,
                'description'=>'便利なメイクアップセット',
                'image_path'=>'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
                'condition'=>'目立った傷や汚れなし',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
        ];

        $user1 = $users[0];
        $user2 = $users[1];
        foreach ($itemsdata as $index => $item) {
            if ($index < 5) {
                $userId = $user1->id;
            } else {
                $userId = $user2->id;
            }

            DB::table('items')->insert([
                'user_id' => $userId,
                'name' => $item['name'],
                'price' => $item['price'],
                'brand' => $item['brand'],
                'description' => $item['description'],
                'image_path' => $item['image_path'],
                'condition' => $item['condition'],
                'is_recommended' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
        }
    }
}
