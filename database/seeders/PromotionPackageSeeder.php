<?php

namespace Database\Seeders;

use App\Models\PromotionPackage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PromotionPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'id' => 1,
                'name' => 'عرض لمدة يوم',
                'price' => 50.00,
                'duration_days' => 1,
                'description' => 'عرض الإعلان في الصفحة الرئيسية لمدة يوم واحد',
                'stripe_price_id' => null,
                'is_active' => true,
            ],
            [
                'id' => 2,
                'name' => 'عرض لمدة أسبوع',
                'price' => 300.00,
                'duration_days' => 7,
                'description' => 'عرض الإعلان في الصفحة الرئيسية لمدة أسبوع كامل',
                'stripe_price_id' => null,
                'is_active' => true,
            ],
            [
                'id' => 3,
                'name' => 'عرض لمدة شهر',
                'price' => 1000.00,
                'duration_days' => 30,
                'description' => 'عرض الإعلان في الصفحة الرئيسية لمدة شهر كامل',
                'stripe_price_id' => null,
                'is_active' => true,
            ]
        ];

        foreach ($packages as $packageData) {
            PromotionPackage::updateOrCreate(['id' => $packageData['id']], $packageData);
        }
    }
}
