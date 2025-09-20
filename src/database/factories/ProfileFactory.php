<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Profile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $prefectures = [
            '北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県',
            '茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県',
            '新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県',
            '静岡県', '愛知県', '三重県', '滋賀県', '京都府', '大阪府', '兵庫県',
            '奈良県', '和歌山県', '鳥取県', '島根県', '岡山県', '広島県', '山口県',
            '徳島県', '香川県', '愛媛県', '高知県', '福岡県', '佐賀県', '長崎県',
            '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県'
        ];

        $cities = [
            '渋谷区', '新宿区', '港区', '中央区', '千代田区', '品川区', '目黒区',
            '世田谷区', '杉並区', '中野区', '豊島区', '北区', '荒川区', '台東区',
            '墨田区', '江東区', '江戸川区', '葛飾区', '足立区', '荒川区'
        ];

        $buildingTypes = [
            'マンション', 'アパート', 'ビル', 'コーポ', 'ハイツ', 'レジデンス',
            'タワー', 'ガーデン', 'パーク', 'ヒルズ', 'プラザ', 'スクエア'
        ];

        return [
            'postal_code' => sprintf('%03d-%04d', $this->faker->numberBetween(100, 999), $this->faker->numberBetween(1000, 9999)),
            'address' => $this->faker->randomElement($prefectures) .
                        $this->faker->randomElement($cities) .
                        $this->faker->streetAddress(),
            'building_name' => $this->faker->randomElement($buildingTypes) .
            $this->faker->lastName() .
            $this->faker->optional(0.7)->numberBetween(1, 9),
        ];
    }
}
