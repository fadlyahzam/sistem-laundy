<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Driver;
use App\Models\DriverLocation;
use App\Models\Invoice;
use App\Models\Kategori;
use App\Models\Layanan;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\StatusLog;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with demo accounts, services, and transactions.
     */
    public function run(): void
    {
        // 1. Seed Users (Admin, Driver, Pelanggan)
        $admin = User::firstOrCreate(
            ['email' => 'admin@laundryku.com'],
            [
                'name' => 'Administrator Laundry',
                'phone' => '081211112222',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $driverUser1 = User::firstOrCreate(
            ['email' => 'driver@laundryku.com'],
            [
                'name' => 'Rian Pratama',
                'phone' => '081233334444',
                'password' => Hash::make('password'),
                'role' => 'driver',
                'is_active' => true,
            ]
        );

        $driver1 = Driver::firstOrCreate(
            ['id_user' => $driverUser1->id_user],
            [
                'plate_number' => 'B 1234 XYZ',
                'vehicle_type' => 'Motor Honda Beat 2024',
                'availability' => 'available',
            ]
        );

        DriverLocation::updateOrCreate(
            ['id_driver' => $driver1->id_driver],
            [
                'latitude' => -6.175392,
                'longitude' => 106.827153,
                'updated_at' => Carbon::now(),
            ]
        );

        $driverUser2 = User::firstOrCreate(
            ['email' => 'driver2@laundryku.com'],
            [
                'name' => 'Agus Santoso',
                'phone' => '081255556666',
                'password' => Hash::make('password'),
                'role' => 'driver',
                'is_active' => true,
            ]
        );

        $driver2 = Driver::firstOrCreate(
            ['id_user' => $driverUser2->id_user],
            [
                'plate_number' => 'B 5678 JKT',
                'vehicle_type' => 'Motor Yamaha NMAX 155',
                'availability' => 'available',
            ]
        );

        $pelanggan = User::firstOrCreate(
            ['email' => 'pelanggan@laundryku.com'],
            [
                'name' => 'Budi Santoso',
                'phone' => '081288889999',
                'password' => Hash::make('password'),
                'role' => 'pelanggan',
                'is_active' => true,
            ]
        );

        $pelanggan2 = User::firstOrCreate(
            ['email' => 'siti@laundryku.com'],
            [
                'name' => 'Siti Rahmawati',
                'phone' => '081277778888',
                'password' => Hash::make('password'),
                'role' => 'pelanggan',
                'is_active' => true,
            ]
        );

        // 2. Seed Layanan & Kategori
        $layanan1 = Layanan::firstOrCreate(
            ['name' => 'Cuci Komplit Reguler (Cuci + Kering + Setrika)'],
            [
                'service_type' => 'kiloan',
                'price_per_kg' => 8000,
                'is_active' => true,
            ]
        );

        Kategori::firstOrCreate(['id_layanan' => $layanan1->id_layanan, 'name' => 'Pakaian Sehari-hari'], ['unit_tariff' => 0, 'is_active' => true]);
        Kategori::firstOrCreate(['id_layanan' => $layanan1->id_layanan, 'name' => 'Bedcover Sedang'], ['unit_tariff' => 15000, 'is_active' => true]);
        Kategori::firstOrCreate(['id_layanan' => $layanan1->id_layanan, 'name' => 'Selimut Tebal'], ['unit_tariff' => 10000, 'is_active' => true]);
        Kategori::firstOrCreate(['id_layanan' => $layanan1->id_layanan, 'name' => 'Boneka Sedang'], ['unit_tariff' => 12000, 'is_active' => true]);

        $layanan2 = Layanan::firstOrCreate(
            ['name' => 'Cuci Kilat Express 6 Jam'],
            [
                'service_type' => 'kiloan',
                'price_per_kg' => 15000,
                'is_active' => true,
            ]
        );

        Kategori::firstOrCreate(['id_layanan' => $layanan2->id_layanan, 'name' => 'Pakaian Regular Express'], ['unit_tariff' => 0, 'is_active' => true]);
        Kategori::firstOrCreate(['id_layanan' => $layanan2->id_layanan, 'name' => 'Prioritas Mesin Sendiri'], ['unit_tariff' => 5000, 'is_active' => true]);

        $layanan3 = Layanan::firstOrCreate(
            ['name' => 'Dry Clean & Satuan Premium'],
            [
                'service_type' => 'satuan',
                'price_per_kg' => 25000,
                'is_active' => true,
            ]
        );

        Kategori::firstOrCreate(['id_layanan' => $layanan3->id_layanan, 'name' => 'Jas / Blazer Formal'], ['unit_tariff' => 25000, 'is_active' => true]);
        Kategori::firstOrCreate(['id_layanan' => $layanan3->id_layanan, 'name' => 'Gaun Pesta / Kebaya'], ['unit_tariff' => 35000, 'is_active' => true]);
        Kategori::firstOrCreate(['id_layanan' => $layanan3->id_layanan, 'name' => 'Sepatu Sneakers'], ['unit_tariff' => 30000, 'is_active' => true]);

        // 3. Seed Realistic Sample Orders for Demo Testing
        
        // Order A: Baru Masuk (MENUNGGU_KONFIRMASI)
        $orderA = Order::firstOrCreate(
            ['order_code' => 'LK-20261008-1001'],
            [
                'id_user' => $pelanggan->id_user,
                'customer_name' => $pelanggan->name,
                'customer_phone' => $pelanggan->phone,
                'address_text' => 'Jl. Kebon Sirih No. 15, Menteng, Jakarta Pusat',
                'latitude' => -6.182500,
                'longitude' => 106.829000,
                'distance_km' => 1.25,
                'notes' => 'Tolong jemput sebelum jam 12 siang ya mas.',
                'pickup_schedule' => Carbon::now()->addHours(2),
                'status' => Order::STATUS_MENUNGGU_KONFIRMASI,
            ]
        );

        OrderItem::firstOrCreate(
            ['id_order' => $orderA->id_order],
            [
                'id_layanan' => $layanan1->id_layanan,
                'quantity' => 3.5,
                'price_snapshot' => 8000,
                'subtotal' => 28000,
            ]
        );

        StatusLog::firstOrCreate(
            ['id_order' => $orderA->id_order, 'to_status' => Order::STATUS_MENUNGGU_KONFIRMASI],
            [
                'id_user' => $pelanggan->id_user,
                'from_status' => null,
                'note' => 'Pesanan baru dibuat oleh pelanggan (Jarak: 1.25 KM).',
                'changed_at' => Carbon::now()->subMinutes(15),
            ]
        );

        // Order B: Siap Bayar via QRIS (MENUNGGU_PEMBAYARAN)
        $orderB = Order::firstOrCreate(
            ['order_code' => 'LK-20261008-1002'],
            [
                'id_user' => $pelanggan->id_user,
                'customer_name' => $pelanggan->name,
                'customer_phone' => $pelanggan->phone,
                'address_text' => 'Jl. Sabang No. 8, Thamrin, Jakarta Pusat',
                'latitude' => -6.185500,
                'longitude' => 106.824000,
                'distance_km' => 1.80,
                'notes' => 'Pakaian kerja disetrika licin ya.',
                'pickup_schedule' => Carbon::now()->subHours(3),
                'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
            ]
        );

        $orderBItem = OrderItem::firstOrCreate(
            ['id_order' => $orderB->id_order],
            [
                'id_layanan' => $layanan1->id_layanan,
                'quantity' => 5.0,
                'price_snapshot' => 8000,
                'subtotal' => 40000,
            ]
        );

        $invoiceB = Invoice::firstOrCreate(
            ['id_order' => $orderB->id_order],
            [
                'invoice_number' => 'INV-LK-20261008-0002',
                'total_amount' => 40000,
                'status' => 'unpaid',
                'issued_at' => Carbon::now()->subMinutes(30),
            ]
        );

        $midtransService = app(MidtransService::class);
        $midtransService->createQrisCharge($invoiceB, 30);

        StatusLog::firstOrCreate(
            ['id_order' => $orderB->id_order, 'to_status' => Order::STATUS_SAMPAI_OUTLET],
            [
                'id_user' => $driverUser1->id_user,
                'from_status' => Order::STATUS_LAUNDRY_DIAMBIL,
                'note' => 'Cucian tiba di outlet.',
                'changed_at' => Carbon::now()->subHour(),
            ]
        );

        StatusLog::firstOrCreate(
            ['id_order' => $orderB->id_order, 'to_status' => Order::STATUS_MENUNGGU_PEMBAYARAN],
            [
                'id_user' => $admin->id_user,
                'from_status' => Order::STATUS_SAMPAI_OUTLET,
                'note' => 'Penimbangan selesai: 5.0 kg. Tagihan diterbitkan Rp 40.000.',
                'changed_at' => Carbon::now()->subMinutes(30),
            ]
        );

        // Order C: Sudah Selesai (SELESAI)
        $orderC = Order::firstOrCreate(
            ['order_code' => 'LK-20261007-0998'],
            [
                'id_user' => $pelanggan->id_user,
                'customer_name' => $pelanggan->name,
                'customer_phone' => $pelanggan->phone,
                'address_text' => 'Apartemen Menteng Park, Cikini, Jakarta Pusat',
                'latitude' => -6.191500,
                'longitude' => 106.841000,
                'distance_km' => 2.45,
                'notes' => 'Titip di lobby resepsionis.',
                'pickup_schedule' => Carbon::now()->subDays(2),
                'status' => Order::STATUS_SELESAI,
            ]
        );

        OrderItem::firstOrCreate(
            ['id_order' => $orderC->id_order],
            [
                'id_layanan' => $layanan2->id_layanan,
                'quantity' => 4.0,
                'price_snapshot' => 15000,
                'subtotal' => 60000,
            ]
        );

        Invoice::firstOrCreate(
            ['id_order' => $orderC->id_order],
            [
                'invoice_number' => 'INV-LK-20261007-0098',
                'total_amount' => 60000,
                'status' => 'paid',
                'issued_at' => Carbon::now()->subDays(2),
                'paid_at' => Carbon::now()->subDays(2)->addMinutes(12),
            ]
        );
    }
}
