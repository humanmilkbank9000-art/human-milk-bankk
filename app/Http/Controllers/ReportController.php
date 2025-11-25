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

        // Filter to show only accepted/successful records (approved and dispensed)
        $reportType = strtolower($meta['type'] ?? $type);

        if (in_array($reportType, ['requests', 'request'])) {
            $records = collect($payload['records'] ?? []);

            // Only include dispensed requests, exclude pending, approved without dispensing, and declined
            $accepted = $records->filter(function ($r) {
                $status = trim(strtolower($r['status'] ?? ''));
                return $status === 'dispensed';
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

        // Filter to show only accepted/successful records (approved and dispensed)
        $reportType = strtolower($meta['type'] ?? $type);

        if (in_array($reportType, ['requests', 'request'])) {
            $records = collect($payload['records'] ?? []);

            // Only include dispensed requests, exclude pending, approved without dispensing, and declined
            $accepted = $records->filter(function ($r) {
                $status = trim(strtolower($r['status'] ?? ''));
                return $status === 'dispensed';
            })->values();

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
            $totalVolume = $records->sum(function ($r) {
                return (float) ($r['total_volume'] ?? 0);
            });
            $payload['records'] = $records->values();
            $payload['total_volume'] = $totalVolume;
        }

        // Ensure PHP GD extension is available before generating PDF (dompdf requires GD for images)
        if (!extension_loaded('gd') && !function_exists('gd_info')) {
            $msg = 'PDF generation requires the PHP GD extension (ext-gd). Please install/enable the GD extension and restart your webserver, then try again.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => $msg], 500);
            }
            return redirect()->back()->with('error', $msg);
        }

        $pdf = Pdf::loadView('admin.reports.preview', [
            'type' => $meta['type'],
            'view' => $view,
            'data' => $payload,
            'meta' => $meta,
            'isPdf' => true,
        ])->setPaper([0, 0, 612, 936], 'portrait');

        // Render the PDF so we can access the underlying Dompdf canvas and
        // draw the page number centered at the footer. We render here so
        // page numbers appear even if inline PHP is disabled in DOMPDF.
        $pdf->render();

        try {
            $dompdf = $pdf->getDomPDF();
            $canvas = $dompdf->get_canvas();
            $fontMetrics = $dompdf->getFontMetrics();
            $font = $fontMetrics->get_font("DejaVu Sans", "normal");
            $size = 9;
            $text = "Page {PAGE_NUM}";

            $w = $canvas->get_width();
            $h = $canvas->get_height();

            // Use a sample width based on 'Page 1' to compute a stable center
            $sampleText = 'Page 1';
            $textWidth = $fontMetrics->get_text_width($sampleText, $font, $size);
            $x = ($w - $textWidth) / 2;

            // Vertical placement: place inside footer band (adjust as needed)
            $y = $h - 36;

            // Footer color #6b7280 as floats
            $color = [107/255, 114/255, 128/255];
            $canvas->page_text($x, $y, $text, $font, $size, $color);
        } catch (\Throwable $e) {
            // If anything goes wrong, ignore and continue — download will still work.
        }

        return $pdf->download($meta['filename']);
    }
}
