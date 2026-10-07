<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Handle incoming Midtrans Webhook / HTTP notification.
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();

        Log::info('Midtrans Webhook Received:', $payload);

        $orderId = $payload['order_id'] ?? null;
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');

        if (!$orderId || !$statusCode || !$grossAmount) {
            return response()->json([
                'status' => 'error',
                'message' => 'Incomplete webhook notification data.',
            ], 400);
        }

        // Validate signature key
        // In local/sandbox development with test keys, allow skip if signature matches or test key
        $isValidSignature = $this->midtransService->validateSignature(
            $orderId,
            $statusCode,
            $grossAmount,
            $signatureKey
        );

        $isDevMode = config('app.debug') && (empty($this->midtransService->getServerKey()) || str_contains($this->midtransService->getServerKey(), 'TEST-KEY'));

        if (!$isValidSignature && !$isDevMode) {
            Log::warning('Midtrans Webhook: Invalid signature key', [
                'order_id' => $orderId,
                'signature_received' => $signatureKey,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid signature key.',
            ], 403);
        }

        try {
            $result = $this->midtransService->processNotification($payload);

            if (!$result['success']) {
                Log::warning('Midtrans notification processing warning:', $result);
                return response()->json([
                    'status' => 'warning',
                    'message' => $result['message'] ?? 'Notification received but order could not be updated.',
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Notification handled successfully.',
                'data' => $result,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error processing Midtrans webhook: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Server error while processing webhook: ' . $e->getMessage(),
            ], 500);
        }
    }
}
