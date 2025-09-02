<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $japaneseNames = [
            '田中太郎', '佐藤花子', '鈴木一郎', '高橋美咲', '渡辺健太',
            '伊藤愛', '山田次郎', '中村恵', '小林翔太', '加藤優子',
            '吉田大輔', '山本真理', '松本和也', '井上麻衣', '木村健一',
            '林美穂', '斎藤裕子', '森田達也', '池田香織', '阿部正人',
            '石川恵美', '山下智子', '中島健二', '石井美奈', '小川正義',
            '岡田由美', '長谷川健', '近藤美紀', '坂本和也', '福田恵子'
        ];

        return [
            'name' => $this->faker->randomElement($japaneseNames),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('user_pass'),
            'remember_token' => Str::random(10),
            'is_first_login' => false,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
