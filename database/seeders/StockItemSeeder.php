<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StockItem;
use App\Models\StockLog;
use App\Models\Category;
use App\Models\User;

class StockItemSeeder extends Seeder
{
    public function run(): void
    {
        $admin   = User::where('role', 'admin')->first();
        $adminId = $admin?->id ?? 1;

        // Map category name → id
        $cats = Category::pluck('id', 'name');

        $items = [
            // ───── DETERJEN ─────
            ['name'=>'Sabun Bubuk Attack 1kg',        'category'=>'Deterjen',          'unit'=>'kg',     'stock'=>50, 'min_stock'=>10, 'price_per_unit'=>22000, 'supplier'=>'Toko Kimia Sejahtera',      'description'=>'Deterjen bubuk untuk pencucian reguler.'],
            ['name'=>'Rinso Anti Noda 900gr',          'category'=>'Deterjen',          'unit'=>'pcs',    'stock'=>40, 'min_stock'=>8,  'price_per_unit'=>20500, 'supplier'=>'Toko Kimia Sejahtera',      'description'=>'Deterjen bubuk dengan formula anti noda membandel.'],
            ['name'=>'So Klin Cair 1,8 liter',         'category'=>'Deterjen',          'unit'=>'botol',  'stock'=>24, 'min_stock'=>5,  'price_per_unit'=>45000, 'supplier'=>'CV Bersih Jaya',            'description'=>'Deterjen cair premium untuk pakaian berwarna.'],
            ['name'=>'Daia Deterjen Bubuk 1kg',        'category'=>'Deterjen',          'unit'=>'kg',     'stock'=>3,  'min_stock'=>8,  'price_per_unit'=>16500, 'supplier'=>'Toko Kimia Sejahtera',      'description'=>'Deterjen bubuk ekonomis untuk laundry kiloan.'],
            // ───── PEWANGI ─────
            ['name'=>'Molto Ultra 900ml',              'category'=>'Pewangi',           'unit'=>'botol',  'stock'=>30, 'min_stock'=>6,  'price_per_unit'=>35000, 'supplier'=>'CV Bersih Jaya',            'description'=>'Pewangi pakaian dengan beragam varian aroma.'],
            ['name'=>'Nuklir Pewangi 1 liter',         'category'=>'Pewangi',           'unit'=>'liter',  'stock'=>15, 'min_stock'=>4,  'price_per_unit'=>28000, 'supplier'=>'Distributor Laundry Makmur','description'=>'Pewangi laundry konsentrat aroma bunga & fresh.'],
            ['name'=>'Downy Sunrise Fresh 720ml',      'category'=>'Pewangi',           'unit'=>'botol',  'stock'=>0,  'min_stock'=>5,  'price_per_unit'=>40000, 'supplier'=>'CV Bersih Jaya',            'description'=>'Pewangi premium anti kusut dengan wangi tahan lama.'],
            // ───── PELEMBUT ─────
            ['name'=>'Molto Softener Pink 900ml',      'category'=>'Pelembut',          'unit'=>'botol',  'stock'=>20, 'min_stock'=>5,  'price_per_unit'=>30000, 'supplier'=>'CV Bersih Jaya',            'description'=>'Pelembut pakaian yang membuat serat kain lebih lembut.'],
            ['name'=>'Comfort Concentrate 1,5 liter',  'category'=>'Pelembut',          'unit'=>'botol',  'stock'=>2,  'min_stock'=>4,  'price_per_unit'=>48000, 'supplier'=>'Distributor Laundry Makmur','description'=>'Pelembut pakaian konsentrat — satu botol untuk banyak cucian.'],
            // ───── PEMUTIH ─────
            ['name'=>'Bayclin Original 1 liter',       'category'=>'Pemutih',           'unit'=>'botol',  'stock'=>18, 'min_stock'=>4,  'price_per_unit'=>18000, 'supplier'=>'Toko Kimia Sejahtera',      'description'=>'Pemutih pakaian putih polos.'],
            ['name'=>'So Klin Pemutih 1,2 liter',      'category'=>'Pemutih',           'unit'=>'botol',  'stock'=>10, 'min_stock'=>3,  'price_per_unit'=>16500, 'supplier'=>'CV Bersih Jaya',            'description'=>'Pemutih pakaian formula aktif untuk noda kuning dan jamur.'],
            // ───── KEMASAN & PLASTIK ─────
            ['name'=>'Kantong Plastik Laundry Besar (isi 100)', 'category'=>'Kemasan & Plastik','unit'=>'pak', 'stock'=>25,'min_stock'=>5, 'price_per_unit'=>35000,'supplier'=>'Toko Plastik Indah','description'=>'Kantong plastik transparan ukuran besar.'],
            ['name'=>'Kantong Plastik Laundry Sedang (isi 100)','category'=>'Kemasan & Plastik','unit'=>'pak', 'stock'=>30,'min_stock'=>8, 'price_per_unit'=>25000,'supplier'=>'Toko Plastik Indah','description'=>'Kantong plastik ukuran sedang.'],
            ['name'=>'Hanger Baju Plastik (isi 12)',   'category'=>'Kemasan & Plastik', 'unit'=>'lusin',  'stock'=>40, 'min_stock'=>10, 'price_per_unit'=>18000, 'supplier'=>'Toko Plastik Indah',        'description'=>'Hanger plastik ringan.'],
            ['name'=>'Tag Nomor Laundry (isi 1000)',   'category'=>'Kemasan & Plastik', 'unit'=>'pak',    'stock'=>8,  'min_stock'=>2,  'price_per_unit'=>22000, 'supplier'=>'Toko ATK Prima',            'description'=>'Label nomor urut untuk menandai cucian.'],
            // ───── CAIRAN PEMBERSIH ─────
            ['name'=>'Karbol Wangi 1 liter',           'category'=>'Cairan Pembersih',  'unit'=>'botol',  'stock'=>12, 'min_stock'=>3,  'price_per_unit'=>15000, 'supplier'=>'Toko Kimia Sejahtera',      'description'=>'Cairan pembersih lantai dan area kerja laundry.'],
            ['name'=>'Hand Soap Cuci Tangan 500ml',    'category'=>'Cairan Pembersih',  'unit'=>'botol',  'stock'=>6,  'min_stock'=>2,  'price_per_unit'=>18000, 'supplier'=>'CV Bersih Jaya',            'description'=>'Sabun cuci tangan cair untuk karyawan.'],
            // ───── PERALATAN ─────
            ['name'=>'Sikat Noda Pakaian',             'category'=>'Peralatan',         'unit'=>'pcs',    'stock'=>10, 'min_stock'=>3,  'price_per_unit'=>12000, 'supplier'=>'Toko Perlengkapan Rumah',   'description'=>'Sikat khusus untuk menyikat noda membandel.'],
            ['name'=>'Tali Jemuran Nilon 50m',         'category'=>'Peralatan',         'unit'=>'gulung', 'stock'=>5,  'min_stock'=>1,  'price_per_unit'=>45000, 'supplier'=>'Toko Perlengkapan Rumah',   'description'=>'Tali nilon kuat untuk jemuran pakaian.'],
            ['name'=>'Jepitan Jemuran Plastik (isi 36)','category'=>'Peralatan',        'unit'=>'pak',    'stock'=>0,  'min_stock'=>3,  'price_per_unit'=>12000, 'supplier'=>'Toko Perlengkapan Rumah',   'description'=>'Jepitan jemuran plastik kuat, anti karat.'],
            ['name'=>'Sarung Tangan Karet (pasang)',   'category'=>'Peralatan',         'unit'=>'pasang', 'stock'=>15, 'min_stock'=>5,  'price_per_unit'=>8000,  'supplier'=>'Toko Perlengkapan Rumah',   'description'=>'Pelindung tangan saat menangani bahan kimia.'],
        ];

        foreach ($items as $data) {
            $catId    = $cats[$data['category']] ?? null;
            $stockVal = $data['stock'];

            $item = StockItem::create([
                'name'           => $data['name'],
                'category_id'    => $catId,
                'unit'           => $data['unit'],
                'stock'          => $stockVal,
                'min_stock'      => $data['min_stock'],
                'price_per_unit' => $data['price_per_unit'],
                'supplier'       => $data['supplier'] ?? null,
                'description'    => $data['description'] ?? null,
            ]);

            if ($stockVal > 0) {
                StockLog::create([
                    'stock_item_id' => $item->id,
                    'user_id'       => $adminId,
                    'type'          => 'in',
                    'quantity'      => $stockVal,
                    'stock_before'  => 0,
                    'stock_after'   => $stockVal,
                    'note'          => 'Stok awal — data seeder.',
                ]);
            }
        }

        $this->command->info('✅ ' . count($items) . ' barang stok berhasil ditambahkan.');
    }
}
