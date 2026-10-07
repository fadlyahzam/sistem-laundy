<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Driver;
use App\Models\DriverLocation;
use App\Models\Notification;
use App\Models\Order;
use App\Models\StatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DriverController extends Controller
{
    protected function getDriver(): Driver
    {
        $driver = Driver::where('id_user', Auth::id())->first();
        if (!$driver) {
            $driver = Driver::create([
                'id_user' => Auth::id(),
                'plate_number' => 'B 1234 XYZ',
                'vehicle_type' => 'Motor Honda Beat',
                'availability' => 'available',
            ]);
        }
        return $driver;
    }

    public function dashboard()
    {
        $driver = $this->getDriver();

        // Active task
        $activeAssignment = Assignment::with(['order.items.layanan', 'order.user'])
            ->where('id_driver', $driver->id_driver)
            ->whereIn('status', ['pending', 'in_progress'])
            ->latest('id_assignment')
            ->first();

        // Counters
        $todayCompletedCount = Assignment::where('id_driver', $driver->id_driver)
            ->where('status', 'completed')
            ->whereDate('finished_at', Carbon::today())
            ->count();

        $totalCompletedCount = Assignment::where('id_driver', $driver->id_driver)
            ->where('status', 'completed')
            ->count();

        return view('driver.dashboard', compact('driver', 'activeAssignment', 'todayCompletedCount', 'totalCompletedCount'));
    }

    public function orders()
    {
        $driver = $this->getDriver();

        $assignments = Assignment::with(['order.items.layanan', 'order.user'])
            ->where('id_driver', $driver->id_driver)
            ->latest('id_assignment')
            ->paginate(10);

        return view('driver.orders_index', compact('driver', 'assignments'));
    }

    public function showOrder($id)
    {
        $driver = $this->getDriver();

        $assignment = Assignment::with(['order.items.layanan', 'order.user', 'order.statusLogs'])
            ->where('id_driver', $driver->id_driver)
            ->findOrFail($id);

        return view('driver.order_detail', compact('driver', 'assignment'));
    }

    public function updateTaskStatus(Request $request, $id)
    {
        $driver = $this->getDriver();
        $assignment = Assignment::with('order')->where('id_driver', $driver->id_driver)->findOrFail($id);
        $order = $assignment->order;

        $targetStatus = $request->input('target_status');

        DB::beginTransaction();
        try {
            $fromStatus = $order->status;

            if ($targetStatus === 'LAUNDRY_DIAMBIL') {
                $assignment->update([
                    'status' => 'in_progress',
                    'assigned_at' => $assignment->assigned_at ?? Carbon::now(),
                ]);
                $order->update(['status' => Order::STATUS_LAUNDRY_DIAMBIL]);
                $note = 'Driver telah menjemput pakaian dari lokasi pelanggan.';
            } elseif ($targetStatus === 'SAMPAI_OUTLET') {
                $assignment->update([
                    'status' => 'completed',
                    'finished_at' => Carbon::now(),
                ]);
                $order->update(['status' => Order::STATUS_SAMPAI_OUTLET]);
                $note = 'Driver telah tiba di outlet dengan membawa pakaian pelanggan.';
                $driver->update(['availability' => 'available']);
            } elseif ($targetStatus === 'MENUNGGU_PENGANTARAN') {
                $assignment->update([
                    'status' => 'in_progress',
                    'assigned_at' => $assignment->assigned_at ?? Carbon::now(),
                ]);
                $order->update(['status' => Order::STATUS_MENUNGGU_PENGANTARAN]);
                $note = 'Driver sedang dalam perjalanan mengantar cucian bersih ke pelanggan.';
            } elseif ($targetStatus === 'SELESAI') {
                $assignment->update([
                    'status' => 'completed',
                    'finished_at' => Carbon::now(),
                ]);
                $order->update(['status' => Order::STATUS_SELESAI]);
                $note = 'Cucian telah sukses diantar dan diterima oleh pelanggan.';
                $driver->update(['availability' => 'available']);
            } else {
                return back()->with('error', 'Status transisi tidak valid.');
            }

            // Create Status Log
            StatusLog::create([
                'id_order' => $order->id_order,
                'id_user' => Auth::id(),
                'from_status' => $fromStatus,
                'to_status' => $order->status,
                'note' => $note,
                'changed_at' => Carbon::now(),
            ]);

            // Notify Customer
            Notification::create([
                'id_user' => $order->id_user,
                'id_order' => $order->id_order,
                'title' => 'Update Status Pesanan',
                'message' => $note,
                'is_read' => false,
            ]);

            DB::commit();

            return redirect()->route('driver.orders.show', $assignment->id_assignment)
                ->with('success', 'Status tugas berhasil diperbarui: ' . $order->status_label);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    public function updateAvailability(Request $request)
    {
        $driver = $this->getDriver();
        $avail = $request->input('availability');

        if (in_array($avail, ['available', 'on_duty', 'offline'])) {
            $driver->update(['availability' => $avail]);
            return back()->with('success', 'Status ketersediaan berhasil diubah ke ' . ucfirst($avail));
        }

        return back()->with('error', 'Status tidak valid.');
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $driver = $this->getDriver();

        DriverLocation::updateOrCreate(
            ['id_driver' => $driver->id_driver],
            [
                'latitude' => (float) $request->latitude,
                'longitude' => (float) $request->longitude,
                'updated_at' => Carbon::now(),
            ]
        );

        return response()->json(['success' => true]);
    }

    public function profile()
    {
        $driver = $this->getDriver();
        $user = Auth::user();

        $totalCompleted = Assignment::where('id_driver', $driver->id_driver)
            ->where('status', 'completed')
            ->count();

        return view('driver.profile', compact('driver', 'user', 'totalCompleted'));
    }
}
