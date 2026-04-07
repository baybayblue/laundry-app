<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ── Profil Toko ──────────────────────────────────
            ['key' => 'store_name',        'value' => 'Laundry Bersih',                     'group' => 'general'],
            ['key' => 'store_tagline',     'value' => 'Bersih, Wangi, Tepat Waktu',         'group' => 'general'],
            ['key' => 'store_description', 'value' => 'Laundry kiloan & satuan terpercaya dengan pelayanan ramah dan hasil bersih berkualitas.',  'group' => 'general'],
            ['key' => 'store_logo',        'value' => '',                                   'group' => 'general'],

            // ── Kontak ───────────────────────────────────────
            ['key' => 'store_phone',       'value' => '0812-3456-7890',                     'group' => 'contact'],
            ['key' => 'store_whatsapp',    'value' => '081234567890',                       'group' => 'contact'],
            ['key' => 'store_email',       'value' => 'admin@laundrybersih.com',            'group' => 'contact'],
            ['key' => 'store_address',     'value' => 'Jl. Kebersihan No. 10, RT 01/RW 02','group' => 'contact'],
            ['key' => 'store_city',        'value' => 'Jakarta Selatan',                   'group' => 'contact'],
            ['key' => 'store_maps_url',    'value' => '',                                   'group' => 'contact'],
            ['key' => 'store_instagram',   'value' => '@laundrybersih',                     'group' => 'contact'],
            ['key' => 'store_facebook',    'value' => '',                                   'group' => 'contact'],

            // ── Jam Operasional ──────────────────────────────
            ['key' => 'op_monday',         'value' => '07:00-21:00',                        'group' => 'operational'],
            ['key' => 'op_tuesday',        'value' => '07:00-21:00',                        'group' => 'operational'],
            ['key' => 'op_wednesday',      'value' => '07:00-21:00',                        'group' => 'operational'],
            ['key' => 'op_thursday',       'value' => '07:00-21:00',                        'group' => 'operational'],
            ['key' => 'op_friday',         'value' => '07:00-21:00',                        'group' => 'operational'],
            ['key' => 'op_saturday',       'value' => '08:00-20:00',                        'group' => 'operational'],
            ['key' => 'op_sunday',         'value' => 'Tutup',                              'group' => 'operational'],

            // ── Transaksi ────────────────────────────────────
            ['key' => 'tax_enabled',       'value' => '0',                                  'group' => 'transaction'],
            ['key' => 'tax_percent',       'value' => '11',                                 'group' => 'transaction'],
            ['key' => 'service_fee',       'value' => '0',                                  'group' => 'transaction'],
            ['key' => 'receipt_footer',    'value' => 'Terima kasih telah mempercayakan cucian Anda kepada kami. 🙏', 'group' => 'transaction'],
            ['key' => 'currency_symbol',   'value' => 'Rp',                                 'group' => 'transaction'],

            // ── Tampilan ─────────────────────────────────────
            ['key' => 'brand_color',       'value' => '#6f42c1',                            'group' => 'appearance'],
            ['key' => 'accent_color',      'value' => '#8b5cf6',                            'group' => 'appearance'],
        ];

        foreach ($settings as $s) {
            Setting::firstOrCreate(['key' => $s['key']], $s);
        }

        $this->command->info('✅ ' . count($settings) . ' pengaturan toko berhasil di-seed.');
    }
}
