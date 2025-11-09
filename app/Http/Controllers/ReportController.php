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

        // The service already filters to only include accepted/successful records
        // No additional filtering needed here

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

        // The service already filters to only include accepted/successful records
        // No additional filtering needed here

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
