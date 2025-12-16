<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'ปากกา Quantum',
            'price' => 15.50,
            'description' => 'ปากกาลูกลื่น หมึกสีน้ำเงิน ขนาด 0.5 mm'
        ]);

        Product::create([
            'name' => 'ยางลบ Mono',
            'price' => 30.00,
            'description' => 'ยางลบคุณภาพดี ลบสะอาด ไม่ทิ้งรอย'
        ]);

        Product::create([
            'name' => 'สมุด Double A',
            'price' => 25.00,
            'description' => 'สมุดปกแข็ง ขนาด A5 จำนวน 80 แกรม'
        ]);
    }
}
