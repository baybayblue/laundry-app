<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Deterjen',
                'description' => 'Sabun, deterjen bubuk dan cair untuk proses pencucian utama.',
                'color'       => '#0d6efd',
                'icon'        => 'ti-droplet',
            ],
            [
                'name'        => 'Pewangi',
                'description' => 'Produk untuk menambahkan aroma segar pada pakaian setelah dicuci.',
                'color'       => '#6f42c1',
                'icon'        => 'ti-wind',
            ],
            [
                'name'        => 'Pelembut',
                'description' => 'Cairan pelembut pakaian agar serat kain tetap lembut dan tidak kusut.',
                'color'       => '#20c997',
                'icon'        => 'ti-feather',
            ],
            [
                'name'        => 'Pemutih',
                'description' => 'Bahan pemutih untuk pakaian putih dan menghilangkan noda membandel.',
                'color'       => '#fd7e14',
                'icon'        => 'ti-star',
            ],
            [
                'name'        => 'Kemasan & Plastik',
                'description' => 'Kantong plastik, hanger, tag nomor, dan kebutuhan pengemasan pakaian.',
                'color'       => '#198754',
                'icon'        => 'ti-package',
            ],
            [
                'name'        => 'Cairan Pembersih',
                'description' => 'Cairan untuk membersihkan peralatan dan area kerja laundry.',
                'color'       => '#0dcaf0',
                'icon'        => 'ti-bottle',
            ],
            [
                'name'        => 'Peralatan',
                'description' => 'Alat-alat bantu operasional laundry seperti sikat, tali jemuran, dll.',
                'color'       => '#dc3545',
                'icon'        => 'ti-tool',
            ],
        ];

        foreach ($categories as $cat) {
            // Upsert: insert jika belum ada, skip jika sudah ada (dari migration data)
            DB::table('categories')->updateOrInsert(
                ['name' => $cat['name']],
                array_merge($cat, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('✅ ' . count($categories) . ' kategori berhasil di-seed.');
    }
}
