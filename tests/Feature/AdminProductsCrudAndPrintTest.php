<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Kategori;
use App\Models\Layanan;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductsCrudAndPrintTest extends TestCase
{
    use RefreshDatabase;
    public function test_admin_can_view_products_page_with_stats(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $layanan = Layanan::create([
            'name' => 'Cuci Kering Express',
            'service_type' => 'kiloan',
            'price_per_kg' => 12000,
            'is_active' => true,
        ]);

        Kategori::create([
            'id_layanan' => $layanan->id_layanan,
            'name' => 'Bedcover Besar',
            'unit_tariff' => 20000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.produk'));
        $response->assertStatus(200);
        $response->assertSee('Kelola Produk & Layanan', false);
        $response->assertSee('Cuci Kering Express');
        $response->assertSee('Rp 12.000');
        $response->assertSee('Tambah Layanan Baru');
    }

    public function test_admin_can_crud_layanan(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // 1. Create
        $response = $this->actingAs($admin)->post(route('admin.layanan.store'), [
            'name' => 'Cuci Setrika Reguler',
            'service_type' => 'kiloan',
            'price_per_kg' => 9000,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('layanan', [
            'name' => 'Cuci Setrika Reguler',
            'price_per_kg' => 9000,
            'is_active' => 1,
        ]);

        $layanan = Layanan::where('name', 'Cuci Setrika Reguler')->first();

        // 2. Update
        $response = $this->actingAs($admin)->put(route('admin.layanan.update', $layanan->id_layanan), [
            'name' => 'Cuci Setrika Premium',
            'service_type' => 'kiloan',
            'price_per_kg' => 11000,
            'is_active' => 1,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('layanan', [
            'id_layanan' => $layanan->id_layanan,
            'name' => 'Cuci Setrika Premium',
            'price_per_kg' => 11000,
        ]);

        // 3. Toggle Status
        $response = $this->actingAs($admin)->post(route('admin.layanan.toggle_status', $layanan->id_layanan));
        $response->assertRedirect();
        $layanan->refresh();
        $this->assertFalse((bool) $layanan->is_active);

        // 4. Delete
        $response = $this->actingAs($admin)->delete(route('admin.layanan.delete', $layanan->id_layanan));
        $response->assertRedirect();
        $this->assertDatabaseMissing('layanan', [
            'id_layanan' => $layanan->id_layanan,
        ]);
    }

    public function test_admin_can_crud_kategori(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $layanan = Layanan::create([
            'name' => 'Dry Clean Jas & Gaun',
            'service_type' => 'satuan',
            'price_per_kg' => 25000,
            'is_active' => true,
        ]);

        // 1. Create Kategori
        $response = $this->actingAs($admin)->post(route('admin.kategori.store'), [
            'id_layanan' => $layanan->id_layanan,
            'name' => 'Jas Formal Pria',
            'unit_tariff' => 35000,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('kategori', [
            'id_layanan' => $layanan->id_layanan,
            'name' => 'Jas Formal Pria',
            'unit_tariff' => 35000,
            'is_active' => 1,
        ]);

        $kat = Kategori::where('name', 'Jas Formal Pria')->first();

        // 2. Update Kategori
        $response = $this->actingAs($admin)->put(route('admin.kategori.update', $kat->id_kategori), [
            'name' => 'Jas Formal Pria & Dasi',
            'unit_tariff' => 40000,
            'is_active' => 1,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('kategori', [
            'id_kategori' => $kat->id_kategori,
            'name' => 'Jas Formal Pria & Dasi',
            'unit_tariff' => 40000,
        ]);

        // 3. Toggle Status Kategori
        $response = $this->actingAs($admin)->post(route('admin.kategori.toggle_status', $kat->id_kategori));
        $response->assertRedirect();
        $kat->refresh();
        $this->assertFalse((bool) $kat->is_active);

        // 4. Delete Kategori
        $response = $this->actingAs($admin)->delete(route('admin.kategori.delete', $kat->id_kategori));
        $response->assertRedirect();
        $this->assertDatabaseMissing('kategori', [
            'id_kategori' => $kat->id_kategori,
        ]);
    }

    public function test_pelanggan_can_view_printable_invoice_and_receipt(): void
    {
        $customer = User::factory()->create(['role' => 'pelanggan']);
        
        $layanan = Layanan::create([
            'name' => 'Cuci Kilat Bersih',
            'service_type' => 'kiloan',
            'price_per_kg' => 10000,
            'is_active' => true,
        ]);

        $order = Order::create([
            'id_user' => $customer->id_user,
            'order_code' => 'ORD-TEST-99',
            'customer_name' => $customer->name,
            'customer_phone' => '081299998888',
            'address_text' => 'Jl. Kebon Jeruk No. 12',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'distance_km' => 2.5,
            'pickup_schedule' => now()->addHours(2),
            'status' => Order::STATUS_DIBAYAR,
        ]);

        OrderItem::create([
            'id_order' => $order->id_order,
            'id_layanan' => $layanan->id_layanan,
            'quantity' => 3.5,
            'price_snapshot' => 10000,
            'subtotal' => 35000,
        ]);

        $invoice = Invoice::create([
            'id_order' => $order->id_order,
            'invoice_number' => 'INV-TEST-99',
            'total_amount' => 35000,
            'status' => 'paid',
            'issued_at' => now(),
            'paid_at' => now(),
        ]);

        $payment = Payment::create([
            'id_invoice' => $invoice->id_invoice,
            'gateway_order_id' => 'MIDTRANS-ORD-TEST-99',
            'amount' => 35000,
            'status' => 'paid',
            'paid_at' => now(),
            'qr_string' => 'demo-qr',
        ]);

        // 1. Printable Invoice View
        $resInvoice = $this->actingAs($customer)->get(route('pelanggan.orders.print_invoice', $order->id_order));
        $resInvoice->assertStatus(200);
        $resInvoice->assertSee('FAKTUR TAGIHAN');
        $resInvoice->assertSee('INV-TEST-99');
        $resInvoice->assertSee('ORD-TEST-99');
        $resInvoice->assertSee('Cuci Kilat Bersih');
        $resInvoice->assertSee('Rp 35.000');
        $resInvoice->assertSee('Tiga Puluh Lima Ribu Rupiah');
        $resInvoice->assertSee('id="btn-kembali"', false);
        $resInvoice->assertSee(route('pelanggan.pesanan.pay', ['id' => $order->id_order, 'tab' => 'invoice']));

        // 2. Printable Receipt View
        $resReceipt = $this->actingAs($customer)->get(route('pelanggan.orders.print_receipt', $order->id_order));
        $resReceipt->assertStatus(200);
        $resReceipt->assertSee('Kuitansi / Bukti Pembayaran Resmi');
        $resReceipt->assertSee('LUNAS DIVERIFIKASI');
        $resReceipt->assertSee('MIDTRANS-ORD-TEST-99');
        $resReceipt->assertSee('Rp 35.000');
        $resReceipt->assertSee('Tiga Puluh Lima Ribu Rupiah');
        $resReceipt->assertSee('id="btn-kembali"', false);
        $resReceipt->assertSee(route('pelanggan.pesanan.pay', ['id' => $order->id_order, 'tab' => 'qris']));

        // 3. Payment View (no target="_blank" so user stays in same tab and can easily go back)
        $resPay = $this->actingAs($customer)->get(route('pelanggan.pesanan.pay', $order->id_order));
        $resPay->assertStatus(200);
        $resPay->assertDontSee('target="_blank"');
    }

    public function test_admin_can_edit_and_delete_driver(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // 1. Create Driver
        $response = $this->actingAs($admin)->post(route('admin.drivers.store'), [
            'name' => 'Budi Driver',
            'email' => 'budi@laundryku.com',
            'phone' => '081234567890',
            'vehicle_type' => 'Honda Beat',
            'plate_number' => 'B 1234 ABC',
            'password' => 'secret123',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'budi@laundryku.com', 'role' => 'driver']);
        $this->assertDatabaseHas('drivers', ['plate_number' => 'B 1234 ABC']);

        $driver = \App\Models\Driver::where('plate_number', 'B 1234 ABC')->first();

        // 2. Edit Driver
        $response = $this->actingAs($admin)->put(route('admin.drivers.update', $driver->id_driver), [
            'name' => 'Budi Santoso (Senior)',
            'email' => 'budi@laundryku.com',
            'phone' => '081299991111',
            'vehicle_type' => 'Yamaha NMAX',
            'plate_number' => 'B 9999 XYZ',
            'availability' => 'on_duty',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['name' => 'Budi Santoso (Senior)', 'phone' => '081299991111']);
        $this->assertDatabaseHas('drivers', [
            'id_driver' => $driver->id_driver,
            'vehicle_type' => 'Yamaha NMAX',
            'plate_number' => 'B 9999 XYZ',
            'availability' => 'on_duty',
        ]);

        // 3. View Drivers Page
        $resPage = $this->actingAs($admin)->get(route('admin.drivers'));
        $resPage->assertStatus(200);
        $resPage->assertSee('Budi Santoso (Senior)');
        $resPage->assertSee('Yamaha NMAX');
        $resPage->assertSee('B 9999 XYZ');
        $resPage->assertSee('Edit');
        $resPage->assertSee('Hapus');

        // 4. Delete Driver
        $response = $this->actingAs($admin)->delete(route('admin.drivers.delete', $driver->id_driver));
        $response->assertRedirect();
        $this->assertDatabaseMissing('drivers', ['id_driver' => $driver->id_driver]);
        $this->assertDatabaseMissing('users', ['email' => 'budi@laundryku.com']);
    }
}
