<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    private array $defaults = [
        // ── Profil Toko ────────────────────────
        'store_name'        => ['value' => 'Laundry Bersih',      'group' => 'general'],
        'store_tagline'     => ['value' => 'Bersih, Wangi, Tepat Waktu', 'group' => 'general'],
        'store_description' => ['value' => '',                    'group' => 'general'],
        'store_logo'        => ['value' => '',                    'group' => 'general'],
        'store_favicon'     => ['value' => '',                    'group' => 'general'],

        // ── Kontak ─────────────────────────────
        'store_phone'       => ['value' => '08123456789',         'group' => 'contact'],
        'store_whatsapp'    => ['value' => '08123456789',         'group' => 'contact'],
        'store_email'       => ['value' => '',                    'group' => 'contact'],
        'store_address'     => ['value' => '',                    'group' => 'contact'],
        'store_city'        => ['value' => '',                    'group' => 'contact'],
        'store_maps_url'    => ['value' => '',                    'group' => 'contact'],
        'store_instagram'   => ['value' => '',                    'group' => 'contact'],
        'store_facebook'    => ['value' => '',                    'group' => 'contact'],

        // ── Jam Operasional ────────────────────
        'op_monday'         => ['value' => '07:00-21:00',         'group' => 'operational'],
        'op_tuesday'        => ['value' => '07:00-21:00',         'group' => 'operational'],
        'op_wednesday'      => ['value' => '07:00-21:00',         'group' => 'operational'],
        'op_thursday'       => ['value' => '07:00-21:00',         'group' => 'operational'],
        'op_friday'         => ['value' => '07:00-21:00',         'group' => 'operational'],
        'op_saturday'       => ['value' => '07:00-21:00',         'group' => 'operational'],
        'op_sunday'         => ['value' => 'Tutup',               'group' => 'operational'],

        // ── Transaksi ──────────────────────────
        'tax_enabled'       => ['value' => '0',                   'group' => 'transaction'],
        'tax_percent'       => ['value' => '11',                  'group' => 'transaction'],
        'service_fee'       => ['value' => '0',                   'group' => 'transaction'],
        'receipt_footer'    => ['value' => 'Terima kasih telah mempercayakan cucian Anda kepada kami. 🙏', 'group' => 'transaction'],
        'currency_symbol'   => ['value' => 'Rp',                  'group' => 'transaction'],

        // ── Tampilan ───────────────────────────
        'brand_color'       => ['value' => '#6f42c1',             'group' => 'appearance'],
        'accent_color'      => ['value' => '#8b5cf6',             'group' => 'appearance'],
    ];

    public function index()
    {
        $settings = Setting::allFlat();

        // Merge dengan default untuk field yang belum ada
        foreach ($this->defaults as $key => $meta) {
            if (!array_key_exists($key, $settings)) {
                $settings[$key] = $meta['value'];
            }
        }

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'store_name'     => 'required|string|max:100',
            'store_phone'    => 'nullable|string|max:20',
            'store_whatsapp' => 'nullable|string|max:20',
            'store_email'    => 'nullable|email|max:100',
            'tax_percent'    => 'nullable|numeric|min:0|max:100',
            'service_fee'    => 'nullable|numeric|min:0',
            'brand_color'    => 'nullable|string|max:7',
            'accent_color'   => 'nullable|string|max:7',
            'store_logo'     => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('store_logo')) {
            $old = Setting::get('store_logo');
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
            $path = $request->file('store_logo')->store('settings', 'public');
            Setting::set('store_logo', $path, 'general');
        }

        // Save setiap field dari defaults yang ada di request
        foreach ($this->defaults as $key => $meta) {
            if ($key === 'store_logo') continue; // handled above

            $val = match ($key) {
                'tax_enabled' => $request->boolean('tax_enabled') ? '1' : '0',
                default        => $request->input($key, $meta['value']) ?? '',
            };
            Setting::set($key, $val, $meta['group']);
        }

        // Clear all cache
        foreach (array_keys($this->defaults) as $key) {
            Cache::forget("setting_{$key}");
        }

        return back()->with('success', 'Pengaturan toko berhasil disimpan!');
    }
}
