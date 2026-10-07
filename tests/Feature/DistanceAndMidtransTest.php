<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\DistanceService;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DistanceAndMidtransTest extends TestCase
{
    use RefreshDatabase;

    public function test_distance_service_calculates_and_validates_radius(): void
    {
        $service = new DistanceService();

        // Near Monas (~0.6 km)
        $nearLat = -6.178000;
        $nearLng = 106.829000;
        $nearResult = $service->validateDistance($nearLat, $nearLng);

        $this->assertTrue($nearResult['is_valid']);
        $this->assertLessThan(2.0, $nearResult['distance_km']);
        $this->assertNull($nearResult['message']);

        // Far location (~45 km to Bogor)
        $farLat = -6.595038;
        $farLng = 106.816635;
        $farResult = $service->validateDistance($farLat, $farLng);

        $this->assertFalse($farResult['is_valid']);
        $this->assertGreaterThan(20.0, $farResult['distance_km']);
        $this->assertEquals(DistanceService::OUT_OF_RANGE_MESSAGE, $farResult['message']);

        // Ensure exception thrown
        $this->expectException(ValidationException::class);
        $service->ensureWithinRadius($farLat, $farLng);
    }

    public function test_midtrans_service_validates_sha512_signature(): void
    {
        $service = new MidtransService();
        $serverKey = $service->getServerKey();

        $orderId = 'INV-20261008-001';
        $statusCode = '200';
        $grossAmount = '50000.00';
        $validSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        $invalidSignature = 'wrong-signature-hash';

        $this->assertTrue($service->validateSignature($orderId, $statusCode, $grossAmount, $validSignature));
        $this->assertFalse($service->validateSignature($orderId, $statusCode, $grossAmount, $invalidSignature));
    }

    public function test_midtrans_webhook_updates_order_status_to_dibayar(): void
    {
        $user = User::factory()->create([
            'phone' => '08123456789',
            'role' => 'pelanggan',
        ]);

        $order = Order::create([
            'id_user' => $user->id_user,
            'order_code' => 'ORD-TEST-001',
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '08123456789',
            'address_text' => 'Jl. Thamrin No. 1, Jakarta Pusat',
            'latitude' => -6.180000,
            'longitude' => 106.830000,
            'distance_km' => 1.5,
            'notes' => 'Tolong hati-hati',
            'pickup_schedule' => now()->addDay(),
            'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
        ]);

        $invoice = Invoice::create([
            'id_order' => $order->id_order,
            'invoice_number' => 'INV-TEST-001',
            'total_amount' => 75000,
            'status' => 'unpaid',
            'issued_at' => now(),
        ]);

        $midtransService = app(MidtransService::class);
        $chargeResult = $midtransService->createQrisCharge($invoice);

        $gatewayOrderId = $chargeResult['gateway_order_id'];
        $grossAmount = '75000';
        $statusCode = '200';
        $signatureKey = hash('sha512', $gatewayOrderId . $statusCode . $grossAmount . $midtransService->getServerKey());

        // Send webhook notification
        $response = $this->postJson(route('midtrans.webhook'), [
            'order_id' => $gatewayOrderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);

        // Assert database state updated
        $this->assertDatabaseHas('invoices', [
            'id_invoice' => $invoice->id_invoice,
            'status' => 'paid',
        ]);

        $this->assertDatabaseHas('orders', [
            'id_order' => $order->id_order,
            'status' => Order::STATUS_DIBAYAR,
        ]);

        $this->assertDatabaseHas('status_logs', [
            'id_order' => $order->id_order,
            'from_status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
            'to_status' => Order::STATUS_DIBAYAR,
        ]);

        $this->assertDatabaseHas('notifications', [
            'id_order' => $order->id_order,
            'id_user' => $user->id_user,
        ]);
    }
}
