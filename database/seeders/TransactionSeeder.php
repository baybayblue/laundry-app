<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::all();
        $services  = Service::active()->get();
        $admin     = User::first();

        if ($services->isEmpty()) {
            $this->command->warn('⚠️ Tidak ada layanan aktif. Jalankan ServiceSeeder dulu.');
            return;
        }

        $scenarios = [
            // Cash - paid - done
            ['method'=>'cash', 'pay_status'=>'paid', 'ord_status'=>'done', 'days_ago'=>5],
            // Cash - paid - delivered
            ['method'=>'cash', 'pay_status'=>'paid', 'ord_status'=>'delivered', 'days_ago'=>7],
            // Cash - paid - processing
            ['method'=>'cash', 'pay_status'=>'paid', 'ord_status'=>'processing', 'days_ago'=>1],
            // Online - paid - processing
            ['method'=>'midtrans', 'pay_status'=>'paid', 'ord_status'=>'processing', 'days_ago'=>2, 'pay_type'=>'qris'],
            // Online - pending - pending
            ['method'=>'midtrans', 'pay_status'=>'pending', 'ord_status'=>'pending', 'days_ago'=>0],
            // Cash - paid - done (walk-in)
            ['method'=>'cash', 'pay_status'=>'paid', 'ord_status'=>'done', 'days_ago'=>10, 'walkin'=>true],
            // Online - expired
            ['method'=>'midtrans', 'pay_status'=>'expired', 'ord_status'=>'cancelled', 'days_ago'=>8],
            // Cash - processing
            ['method'=>'cash', 'pay_status'=>'paid', 'ord_status'=>'processing', 'days_ago'=>0],
        ];

        foreach ($scenarios as $i => $sc) {
            $customer     = $sc['walkin'] ?? false ? null : $customers->random();
            $selectedSvcs = $services->random(rand(1, min(3, $services->count())));
            $subtotal     = 0;
            $itemsData    = [];

            foreach ($selectedSvcs as $svc) {
                $qty      = $svc->type === 'flat' ? 1 : round(rand(10, 50) / 10, 1);
                $line     = $svc->type === 'flat' ? (float)$svc->price : round($qty * (float)$svc->price, 2);
                $subtotal += $line;
                $itemsData[] = [
                    'service_id'   => $svc->id,
                    'service_name' => $svc->name,
                    'service_type' => $svc->type,
                    'quantity'     => $qty,
                    'unit_price'   => (float)$svc->price,
                    'subtotal'     => $line,
                ];
            }

            $total = round($subtotal, 2);
            $createdAt = now()->subDays($sc['days_ago'])->subHours(rand(0, 10));

            $trx = Transaction::create([
                'invoice_number'        => Transaction::generateInvoiceNumber(),
                'customer_id'           => $customer?->id,
                'customer_name'         => $customer ? $customer->name : 'Walk-in ' . fake()->firstName(),
                'customer_phone'        => $customer ? $customer->phone : '08' . rand(100000000, 999999999),
                'subtotal'              => $subtotal,
                'discount_amount'       => 0,
                'tax_amount'            => 0,
                'service_fee'           => 0,
                'total_amount'          => $total,
                'payment_method'        => $sc['method'],
                'payment_status'        => $sc['pay_status'],
                'order_status'          => $sc['ord_status'],
                'midtrans_payment_type' => $sc['pay_type'] ?? null,
                'paid_at'               => $sc['pay_status'] === 'paid' ? $createdAt->copy()->addMinutes(rand(5,30)) : null,
                'created_by'            => $admin?->id,
                'created_at'            => $createdAt,
                'updated_at'            => $createdAt,
            ]);

            foreach ($itemsData as $item) {
                $item['transaction_id'] = $trx->id;
                TransactionItem::create(array_merge($item, ['created_at'=>$createdAt,'updated_at'=>$createdAt]));
            }
        }

        $this->command->info('✅ ' . count($scenarios) . ' transaksi demo berhasil dibuat.');
    }
}
