<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

// モデルを使っての書き方（Confirmation-testではDB使ったやり方で記載。どちらでもいいけど推奨してるのモデルを使う方
class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contents = [
                "商品のお届けについて",
                "商品の交換について",
                "商品トラブル",
                "ショップへのお問い合わせ",
                "その他"
        ];

        foreach ($contents as $content) {
            Category::create([
                'content' => $content,
            ]);
        }
    }
}
