<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\Layanan;
use App\Models\Order;
use App\Models\User;
use App\Services\DistanceService;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LaundryFlowEndToEndTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_laundry_workflow_10_statuses(): void
    {
        // 1. Setup Admin, Driver, and Pelanggan
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin_test@laundryku.com',
            'phone' => '0811111111',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $driverUser = User::create([
            'name' => 'Driver Test',
            'email' => 'driver_test@laundryku.com',
            'phone' => '0822222222',
            'password' => bcrypt('password'),
            'role' => 'driver',
            'is_active' => true,
        ]);

        $driver = Driver::create([
            'id_user' => $driverUser->id_user,
            'plate_number' => 'B 9999 TST',
            'vehicle_type' => 'Motor Beat',
            'availability' => 'available',
        ]);

        $pelanggan = User::create([
            'name' => 'Pelanggan Test',
            'email' => 'pelanggan_test@laundryku.com',
            'phone' => '0833333333',
            'password' => bcrypt('password'),
            'role' => 'pelanggan',
            'is_active' => true,
        ]);

        $layanan = Layanan::create([
            'name' => 'Cuci Komplit Reguler',
            'service_type' => 'kiloan',
            'price_per_kg' => 8000,
            'is_active' => true,
        ]);

        // 2. Pelanggan attempts order > 20 KM -> REJECTED
        $responseFar = $this->actingAs($pelanggan)->post(route('pelanggan.orders.store'), [
            'customer_name' => 'Pelanggan Far',
            'customer_phone' => '0833333333',
            'id_layanan' => $layanan->id_layanan,
            'quantity' => 3,
            'address_text' => 'Bogor Kota, Jawa Barat',
            'latitude' => -6.600000,
            'longitude' => 106.800000,
            'pickup_schedule' => Carbon::now()->addHours(2)->toDateTimeString(),
        ]);

        $responseFar->assertSessionHas('error', DistanceService::OUT_OF_RANGE_MESSAGE);
        $this->assertEquals(0, Order::count());

        // 3. Pelanggan places order <= 20 KM -> ACCEPTED (Status 1: MENUNGGU_KONFIRMASI)
        $responseNear = $this->actingAs($pelanggan)->post(route('pelanggan.orders.store'), [
            'customer_name' => 'Pelanggan Dekat',
            'customer_phone' => '0833333333',
            'id_layanan' => $layanan->id_layanan,
            'quantity' => 3,
            'address_text' => 'Jl. MH Thamrin No. 5, Jakarta Pusat',
            'latitude' => -6.185000,
            'longitude' => 106.825000,
            'pickup_schedule' => Carbon::now()->addHours(2)->toDateTimeString(),
        ]);

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertEquals(Order::STATUS_MENUNGGU_KONFIRMASI, $order->status);

        // 4. Admin assigns Pickup Driver (Status 2: DRIVER_DITUGASKAN)
        $this->actingAs($admin)->post(route('admin.orders.assign_driver', $order->id_order), [
            'id_driver' => $driver->id_driver,
            'type' => 'pickup',
        ]);
        $order->refresh();
        $this->assertEquals(Order::STATUS_DRIVER_DITUGASKAN, $order->status);

        // 5. Driver picks up laundry (Status 3: LAUNDRY_DIAMBIL)
        $pickupAssignment = $order->pickupAssignment;
        $this->actingAs($driverUser)->post(route('driver.tasks.update', $pickupAssignment->id_assignment), [
            'target_status' => 'LAUNDRY_DIAMBIL',
        ]);
        $order->refresh();
        $this->assertEquals(Order::STATUS_LAUNDRY_DIAMBIL, $order->status);

        // 6. Driver arrives at outlet (Status 4: SAMPAI_OUTLET)
        $this->actingAs($driverUser)->post(route('driver.tasks.update', $pickupAssignment->id_assignment), [
            'target_status' => 'SAMPAI_OUTLET',
        ]);
        $order->refresh();
        $this->assertEquals(Order::STATUS_SAMPAI_OUTLET, $order->status);

        // 7. Admin weighs clothes (3.5 kg) and issues invoice (Status 5: MENUNGGU_PEMBAYARAN)
        $this->actingAs($admin)->post(route('admin.orders.issue_invoice', $order->id_order), [
            'final_quantity' => 3.5,
        ]);
        $order->refresh();
        $this->assertEquals(Order::STATUS_MENUNGGU_PEMBAYARAN, $order->status);
        $this->assertNotNull($order->invoice);
        $this->assertEquals(28000, (float) $order->invoice->total_amount);

        // 8. Pelanggan pays via Midtrans QRIS -> Webhook settles (Status 6: DIBAYAR)
        $midtransService = app(MidtransService::class);
        $charge = $midtransService->createQrisCharge($order->invoice);
        $gatewayOrderId = $charge['gateway_order_id'];
        $grossAmount = '28000';
        $statusCode = '200';
        $signatureKey = hash('sha512', $gatewayOrderId . $statusCode . $grossAmount . $midtransService->getServerKey());

        $webhookResponse = $this->postJson(route('midtrans.webhook'), [
            'order_id' => $gatewayOrderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
        ]);
        $webhookResponse->assertStatus(200);

        $order->refresh();
        $this->assertEquals(Order::STATUS_DIBAYAR, $order->status);
        $this->assertEquals('paid', $order->invoice->status);

        // 9. Admin starts laundry processing (Status 7: SEDANG_DIPROSES)
        $this->actingAs($admin)->post(route('admin.orders.update_status', $order->id_order), [
            'status' => Order::STATUS_SEDANG_DIPROSES,
        ]);
        $order->refresh();
        $this->assertEquals(Order::STATUS_SEDANG_DIPROSES, $order->status);

        // 10. Packing completed, ready for delivery (Status 8: SIAP_DIANTAR)
        $this->actingAs($admin)->post(route('admin.orders.update_status', $order->id_order), [
            'status' => Order::STATUS_SIAP_DIANTAR,
        ]);
        $order->refresh();
        $this->assertEquals(Order::STATUS_SIAP_DIANTAR, $order->status);

        // 11. Admin assigns Delivery Driver (Status 2 / Delivery assignment)
        $this->actingAs($admin)->post(route('admin.orders.assign_driver', $order->id_order), [
            'id_driver' => $driver->id_driver,
            'type' => 'delivery',
        ]);
        $order->refresh();
        $deliveryAssignment = $order->deliveryAssignment;
        $this->assertNotNull($deliveryAssignment);

        // 12. Driver starts delivering to customer (Status 9: MENUNGGU_PENGANTARAN)
        $this->actingAs($driverUser)->post(route('driver.tasks.update', $deliveryAssignment->id_assignment), [
            'target_status' => 'MENUNGGU_PENGANTARAN',
        ]);
        $order->refresh();
        $this->assertEquals(Order::STATUS_MENUNGGU_PENGANTARAN, $order->status);

        // 13. Driver finishes delivery (Status 10: SELESAI)
        $this->actingAs($driverUser)->post(route('driver.tasks.update', $deliveryAssignment->id_assignment), [
            'target_status' => 'SELESAI',
        ]);
        $order->refresh();
        $this->assertEquals(Order::STATUS_SELESAI, $order->status);
        $this->assertEquals('completed', $deliveryAssignment->fresh()->status);
    }
}
