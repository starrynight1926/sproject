<?php

namespace Database\Seeders;

use App\Models\GoalType;
use App\Models\GoalUnit;
use Illuminate\Database\Seeder;

class GoalLookupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['key' => 'quantity', 'label' => 'Số lượng', 'sort_order' => 0],
            ['key' => 'money', 'label' => 'Tiền', 'sort_order' => 1],
            ['key' => 'percent', 'label' => 'Tỷ lệ %', 'sort_order' => 2],
        ];

        foreach ($types as $type) {
            GoalType::firstOrCreate(['key' => $type['key']], $type);
        }

        $units = [
            'file', 'video', 'hợp đồng', 'nhân sự', 'lượt trả lương',
            'VNĐ', '%', 'người', 'giờ', 'sản phẩm', 'khách hàng', 'đơn hàng',
        ];

        foreach ($units as $index => $name) {
            GoalUnit::firstOrCreate(['name' => $name], ['name' => $name, 'sort_order' => $index]);
        }
    }
}
