<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\ReportService;

class ReportController extends Controller
{
    protected ReportService $service;

    public function __construct(ReportService $service)
    {
        $this->service = $service;
    }
    public function preview(Request $request, string $type)
    {
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);

        [$view, $payload, $meta] = $this->service->buildReportPayload($type, $year, $month);

        // Ensure preview (used for on-screen print) shows only accepted/successful records
        $reportType = strtolower($meta['type'] ?? $type);

        if (in_array($reportType, ['requests', 'request'])) {
            $records = collect($payload['records'] ?? []);

            // Only include approved and dispensed, exclude pending and declined
            $accepted = $records->filter(function ($r) {
                $status = trim(strtolower($r['status'] ?? ''));
                return in_array($status, ['approved', 'dispensed']);
            })->values();

            $payload['records'] = $accepted;
            $payload['total'] = $accepted->count();
            $payload['approved'] = $accepted->count();
            $payload['declined'] = 0;
            $payload['dispensed_volume'] = $accepted->sum(function ($r) {
                return (float) ($r['volume_dispensed'] ?? 0);
            });
        } elseif (in_array($reportType, ['donations', 'donation'])) {
            $records = collect($payload['records'] ?? []);
            // Keep only records that are present (service already filtered successful donations)
            $payload['records'] = $records->values();
            $payload['total_volume'] = $records->sum(function ($r) {
                return (float) ($r['total_volume'] ?? 0);
            });
        }

        return view('admin.reports.preview', [
            'type' => $meta['type'],
            'view' => $view,
            'data' => $payload,
            'meta' => $meta,
            'isPdf' => false,
        ]);
    }

    public function download(Request $request, string $type)
    {
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);

        [$view, $payload, $meta] = $this->service->buildReportPayload($type, $year, $month);

        // When generating PDF/print output, ensure the exported data contains only "accepted" records.
        // For requests: only include 'approved' and 'dispensed' statuses
        $reportType = strtolower($meta['type'] ?? $type);

        if (in_array($reportType, ['requests', 'request'])) {
            $records = collect($payload['records'] ?? []);

            // Only include approved and dispensed, exclude pending and declined
            $accepted = $records->filter(function ($r) {
                $status = trim(strtolower($r['status'] ?? ''));
                return in_array($status, ['approved', 'dispensed']);
            })->values();

            // Recompute summary values expected by the requests report partial
            $total = $accepted->count();
            $approved = $accepted->count();
            $declined = 0;
            $dispensedVolume = $accepted->sum(function ($r) {
                return (float) ($r['volume_dispensed'] ?? 0);
            });

            $payload['records'] = $accepted;
            $payload['total'] = $total;
            $payload['approved'] = $approved;
            $payload['declined'] = $declined;
            $payload['dispensed_volume'] = $dispensedVolume;
        } elseif (in_array($reportType, ['donations', 'donation'])) {
            $records = collect($payload['records'] ?? []);

            // Donation service already filters to successful donations, but ensure totals reflect only those rows
            $totalVolume = $records->sum(function ($r) {
                return (float) ($r['total_volume'] ?? 0);
            });

            $payload['records'] = $records->values();
            $payload['total_volume'] = $totalVolume;
        }

        $pdf = Pdf::loadView('admin.reports.preview', [
            'type' => $meta['type'],
            'view' => $view,
            'data' => $payload,
            'meta' => $meta,
            'isPdf' => true,
        ])->setPaper([0, 0, 612, 936], 'portrait');

        return $pdf->download($meta['filename']);
    }
}
