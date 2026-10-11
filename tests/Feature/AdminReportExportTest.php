<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Tests\TestCase;

class AdminReportExportTest extends TestCase
{
    public function test_admin_can_view_laporan_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.laporan'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Transaksi');
        $response->assertSee('Export Excel');
        $response->assertSee('Export PDF');
    }

    public function test_admin_can_export_excel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.laporan.export_excel'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_export_pdf(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.laporan.export_pdf'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
