<?php

namespace Database\Seeders;

use App\Models\Discount;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        $discounts = [
            [
                'name'            => 'Promo Pelanggan Baru',
                'code'            => 'NEWMEMBER',
                'type'            => 'percentage',
                'value'           => 20,
                'min_transaction' => 30000,
                'max_discount'    => 50000,
                'usage_limit'     => 100,
                'usage_count'     => 12,
                'start_date'      => null,
                'end_date'        => Carbon::now()->addMonths(3)->toDateString(),
                'is_active'       => true,
                'description'     => 'Diskon 20% untuk pelanggan yang mendaftar pertama kali. Berlaku hingga 3 bulan ke depan.',
            ],
            [
                'name'            => 'Diskon Akhir Tahun',
                'code'            => 'ENDYEAR25',
                'type'            => 'percentage',
                'value'           => 25,
                'min_transaction' => 50000,
                'max_discount'    => 75000,
                'usage_limit'     => 50,
                'usage_count'     => 50,
                'start_date'      => Carbon::now()->subMonths(2)->toDateString(),
                'end_date'        => Carbon::now()->subMonth()->toDateString(),
                'is_active'       => true,
                'description'     => 'Promo akhir tahun 25% untuk semua layanan. (Sudah kadaluarsa)',
            ],
            [
                'name'            => 'Flash Sale Cuci Sepatu',
                'code'            => 'SEPATU10K',
                'type'            => 'fixed',
                'value'           => 10000,
                'min_transaction' => 35000,
                'max_discount'    => null,
                'usage_limit'     => 30,
                'usage_count'     => 8,
                'start_date'      => Carbon::now()->toDateString(),
                'end_date'        => Carbon::now()->addDays(7)->toDateString(),
                'is_active'       => true,
                'description'     => 'Potongan Rp 10.000 untuk layanan cuci sepatu selama 1 minggu.',
            ],
            [
                'name'            => 'Member VIP',
                'code'            => 'VIP2026',
                'type'            => 'percentage',
                'value'           => 15,
                'min_transaction' => null,
                'max_discount'    => 100000,
                'usage_limit'     => null,
                'usage_count'     => 45,
                'start_date'      => Carbon::now()->startOfYear()->toDateString(),
                'end_date'        => Carbon::now()->endOfYear()->toDateString(),
                'is_active'       => true,
                'description'     => 'Diskon 15% eksklusif untuk pelanggan Member VIP sepanjang tahun 2026.',
            ],
            [
                'name'            => 'Promo Lebaran',
                'code'            => 'LEBARAN30',
                'type'            => 'percentage',
                'value'           => 30,
                'min_transaction' => 100000,
                'max_discount'    => 150000,
                'usage_limit'     => 200,
                'usage_count'     => 0,
                'start_date'      => Carbon::now()->addMonths(1)->toDateString(),
                'end_date'        => Carbon::now()->addMonths(2)->toDateString(),
                'is_active'       => true,
                'description'     => 'Promo spesial Lebaran 30% untuk transaksi minimal Rp 100.000. (Akan Datang)',
            ],
            [
                'name'            => 'Diskon Nonaktif',
                'code'            => 'PAUSED50',
                'type'            => 'fixed',
                'value'           => 50000,
                'min_transaction' => 200000,
                'max_discount'    => null,
                'usage_limit'     => 10,
                'usage_count'     => 3,
                'start_date'      => null,
                'end_date'        => null,
                'is_active'       => false,
                'description'     => 'Diskon sementara dinonaktifkan oleh admin.',
            ],
        ];

        foreach ($discounts as $data) {
            Discount::firstOrCreate(['code' => $data['code']], $data);
        }
    }
}
