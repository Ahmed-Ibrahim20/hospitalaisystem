<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medicine;
use Carbon\Carbon;

class MedicineSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $medicines = [
            [
                'name' => 'باراسيتامول 500 مجم',
                'generic_name' => 'Paracetamol',
                'description' => 'مسكن للألم وخافض للحرارة',
                'manufacturer' => 'شركة الإسكندرية للأدوية',
                'price' => 15.50,
                'quantity_in_stock' => 100,
                'minimum_stock_level' => 20,
                'unit' => 'قرص',
                'expiry_date' => Carbon::now()->addMonths(18),
                'batch_number' => 'PAR2024001',
                'status' => 'active',
            ],
            [
                'name' => 'أموكسيسيلين 250 مجم',
                'generic_name' => 'Amoxicillin',
                'description' => 'مضاد حيوي واسع المجال',
                'manufacturer' => 'شركة ممفيس للأدوية',
                'price' => 25.00,
                'quantity_in_stock' => 5, // مخزون منخفض
                'minimum_stock_level' => 15,
                'unit' => 'كبسولة',
                'expiry_date' => Carbon::now()->addMonths(12),
                'batch_number' => 'AMX2024002',
                'status' => 'active',
            ],
            [
                'name' => 'شراب الكحة للأطفال',
                'generic_name' => 'Dextromethorphan',
                'description' => 'شراب مهدئ للكحة للأطفال',
                'manufacturer' => 'شركة النيل للأدوية',
                'price' => 18.75,
                'quantity_in_stock' => 50,
                'minimum_stock_level' => 10,
                'unit' => 'شراب',
                'expiry_date' => Carbon::now()->addMonths(24),
                'batch_number' => 'COF2024003',
                'status' => 'active',
            ],
            [
                'name' => 'إنسولين سريع المفعول',
                'generic_name' => 'Insulin Rapid',
                'description' => 'إنسولين لمرضى السكري',
                'manufacturer' => 'شركة نوفو نورديسك',
                'price' => 120.00,
                'quantity_in_stock' => 25,
                'minimum_stock_level' => 5,
                'unit' => 'حقنة',
                'expiry_date' => Carbon::now()->addMonths(6),
                'batch_number' => 'INS2024004',
                'status' => 'active',
            ],
            [
                'name' => 'مرهم مضاد للالتهابات',
                'generic_name' => 'Diclofenac Gel',
                'description' => 'مرهم موضعي مضاد للالتهابات',
                'manufacturer' => 'شركة جلاكسو',
                'price' => 32.50,
                'quantity_in_stock' => 0, // نفد من المخزون
                'minimum_stock_level' => 8,
                'unit' => 'مرهم',
                'expiry_date' => Carbon::now()->addMonths(15),
                'batch_number' => 'DIC2024005',
                'status' => 'active',
            ],
            [
                'name' => 'قطرة عين مضاد حيوي',
                'generic_name' => 'Chloramphenicol Eye Drops',
                'description' => 'قطرة عين لعلاج التهابات العين',
                'manufacturer' => 'شركة الدلتا للأدوية',
                'price' => 22.00,
                'quantity_in_stock' => 30,
                'minimum_stock_level' => 12,
                'unit' => 'قطرة',
                'expiry_date' => Carbon::now()->addMonths(9),
                'batch_number' => 'EYE2024006',
                'status' => 'active',
            ],
            [
                'name' => 'بخاخ الربو',
                'generic_name' => 'Salbutamol Inhaler',
                'description' => 'بخاخ لعلاج نوبات الربو',
                'manufacturer' => 'شركة أسترا زينيكا',
                'price' => 85.00,
                'quantity_in_stock' => 15,
                'minimum_stock_level' => 6,
                'unit' => 'بخاخ',
                'expiry_date' => Carbon::now()->addMonths(20),
                'batch_number' => 'SAL2024007',
                'status' => 'active',
            ],
            [
                'name' => 'فيتامين د 1000 وحدة',
                'generic_name' => 'Vitamin D3',
                'description' => 'مكمل غذائي فيتامين د',
                'manufacturer' => 'شركة فاركو للأدوية',
                'price' => 45.00,
                'quantity_in_stock' => 80,
                'minimum_stock_level' => 25,
                'unit' => 'قرص',
                'expiry_date' => Carbon::now()->addMonths(30),
                'batch_number' => 'VIT2024008',
                'status' => 'active',
            ],
            [
                'name' => 'دواء منتهي الصلاحية',
                'generic_name' => 'Expired Medicine',
                'description' => 'دواء تجريبي منتهي الصلاحية',
                'manufacturer' => 'شركة تجريبية',
                'price' => 10.00,
                'quantity_in_stock' => 20,
                'minimum_stock_level' => 5,
                'unit' => 'قرص',
                'expiry_date' => Carbon::now()->subMonths(2), // منتهي الصلاحية
                'batch_number' => 'EXP2023001',
                'status' => 'expired',
            ],
            [
                'name' => 'دواء غير نشط',
                'generic_name' => 'Inactive Medicine',
                'description' => 'دواء تجريبي غير نشط',
                'manufacturer' => 'شركة تجريبية',
                'price' => 8.00,
                'quantity_in_stock' => 10,
                'minimum_stock_level' => 3,
                'unit' => 'كبسولة',
                'expiry_date' => Carbon::now()->addMonths(12),
                'batch_number' => 'INA2024009',
                'status' => 'inactive',
            ],
        ];

        foreach ($medicines as $medicine) {
            Medicine::create($medicine);
        }
    }
}
