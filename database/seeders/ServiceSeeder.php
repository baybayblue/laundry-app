<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name'            => 'Cuci Reguler',
                'type'            => 'per_kg',
                'price'           => 7000,
                'estimated_hours' => 48,
                'description'     => 'Layanan cuci standar dengan deterjen berkualitas. Dicuci, dikeringkan, dan dilipat rapi.',
                'color'           => '#0d6efd',
                'icon'            => 'ti-wash',
                'is_active'       => true,
            ],
            [
                'name'            => 'Cuci Express',
                'type'            => 'per_kg',
                'price'           => 12000,
                'estimated_hours' => 6,
                'description'     => 'Layanan kilat 6 jam selesai. Cocok untuk kebutuhan mendesak dengan hasil tetap bersih.',
                'color'           => '#dc3545',
                'icon'            => 'ti-bolt',
                'is_active'       => true,
            ],
            [
                'name'            => 'Cuci + Setrika',
                'type'            => 'per_kg',
                'price'           => 10000,
                'estimated_hours' => 48,
                'description'     => 'Paket lengkap: cuci bersih, keringkan, dan setrika hingga rapi siap pakai.',
                'color'           => '#6f42c1',
                'icon'            => 'ti-shirt',
                'is_active'       => true,
            ],
            [
                'name'            => 'Setrika Saja',
                'type'            => 'per_kg',
                'price'           => 5000,
                'estimated_hours' => 24,
                'description'     => 'Layanan setrika khusus untuk pakaian yang sudah bersih. Rapi dan anti kusut.',
                'color'           => '#fd7e14',
                'icon'            => 'ti-temperature',
                'is_active'       => true,
            ],
            [
                'name'            => 'Dry Clean',
                'type'            => 'per_pcs',
                'price'           => 25000,
                'estimated_hours' => 72,
                'description'     => 'Pembersihan kering untuk pakaian sensitif seperti jas, gaun, dan bahan premium.',
                'color'           => '#198754',
                'icon'            => 'ti-droplet-off',
                'is_active'       => true,
            ],
            [
                'name'            => 'Cuci Sepatu',
                'type'            => 'per_pcs',
                'price'           => 35000,
                'estimated_hours' => 48,
                'description'     => 'Cuci sepatu menggunakan bahan khusus, aman untuk berbagai material termasuk kulit & kanvas.',
                'color'           => '#20c997',
                'icon'            => 'ti-shoe',
                'is_active'       => true,
            ],
            [
                'name'            => 'Cuci Boneka / Selimut',
                'type'            => 'flat',
                'price'           => 50000,
                'estimated_hours' => 72,
                'description'     => 'Layanan cuci khusus untuk boneka besar, selimut, dan bed cover dengan harga tetap.',
                'color'           => '#e83e8c',
                'icon'            => 'ti-heart',
                'is_active'       => true,
            ],
        ];

        foreach ($services as $data) {
            Service::firstOrCreate(['name' => $data['name']], $data);
        }
    }
}
