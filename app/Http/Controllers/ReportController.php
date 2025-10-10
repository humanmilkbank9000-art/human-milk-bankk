<?php

namespace App\Http\Controllers;

use App\Models\DispensedMilk;
use App\Models\Donation;
use App\Models\PasteurizationBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function preview(Request $request, string $type)
    {
        [$view, $payload, $meta] = $this->buildReportPayload($type, $request);

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
        [$view, $payload, $meta] = $this->buildReportPayload($type, $request);

        $pdf = Pdf::loadView('admin.reports.preview', [
            'type' => $meta['type'],
            'view' => $view,
            'data' => $payload,
            'meta' => $meta,
            'isPdf' => true,
        ])->setPaper([0, 0, 612, 936], 'portrait');

        return $pdf->download($meta['filename']);
    }

    protected function buildReportPayload(string $type, Request $request): array
    {
    $normalizedType = strtolower($type);
    $timezone = config('app.timezone', 'UTC');
    $now = Carbon::now($timezone);

    $year = (int) ($request->input('year', $now->year));
    $month = (int) ($request->input('month', $now->month));

        if ($month < 1 || $month > 12) {
            $month = $now->month;
        }

        $period = Carbon::create($year, $month, 1, 0, 0, 0, $timezone);
        $periodLabel = $period->translatedFormat('F Y');

        $meta = [
            'type' => $normalizedType,
            'year' => $year,
            'month' => $month,
            'period' => $period,
            'periodLabel' => $periodLabel,
            'generatedAt' => $now,
            'timezoneName' => $timezone,
            'timezoneAbbr' => $now->format('T'),
        ];

        switch ($normalizedType) {
            case 'requests':
            case 'request':
                $meta['title'] = 'Breastmilk Request Report';
                $meta['filename'] = $this->buildFilename('Breastmilk Request Report', $period);
                return [
                    'admin.reports.requests',
                    $this->buildRequestData($year, $month),
                    $meta,
                ];
            case 'donations':
            case 'donation':
                $meta['title'] = 'Breastmilk Donation Report';
                $meta['filename'] = $this->buildFilename('Breastmilk Donation Report', $period);
                return [
                    'admin.reports.donations',
                    $this->buildDonationData($year, $month),
                    $meta,
                ];
            case 'inventory':
                $meta['title'] = 'Breastmilk Inventory Report';
                $meta['filename'] = $this->buildFilename('Breastmilk Inventory Report', $period);
                return [
                    'admin.reports.inventory',
                    $this->buildInventoryData($year, $month),
                    $meta,
                ];
            default:
                abort(404, 'Report type not found.');
        }
    }

    protected function buildRequestData(int $year, int $month): array
    {
        $dispensed = DispensedMilk::with([
            'guardian',
            'recipient',
            'breastmilkRequest.user',
            'breastmilkRequest.infant',
            'sourceDonations.user',
            'sourceBatches',
        ])
            ->whereYear('date_dispensed', $year)
            ->whereMonth('date_dispensed', $month)
            ->orderBy('date_dispensed')
            ->orderBy('time_dispensed')
            ->get();

        $records = $dispensed->map(function (DispensedMilk $milk) {
            $guardian = $milk->guardian ?: optional($milk->breastmilkRequest)->user;
            $recipient = $milk->recipient ?: optional($milk->breastmilkRequest)->infant;

            $donorLabels = $milk->sourceDonations->map(function (Donation $donation) {
                $user = $donation->user;
                $name = $user ? $this->formatFullName($user) : null;
                if (!$name && !empty($donation->donor_name)) {
                    $name = $donation->donor_name;
                }
                return $name ?: 'Donation #' . $donation->breastmilk_donation_id;
            });

            if ($donorLabels->isEmpty()) {
                $donorLabels = $milk->sourceBatches->map(function (PasteurizationBatch $batch) {
                    return 'Batch ' . ($batch->batch_number ?? $batch->batch_id);
                });
            }

            $milkType = 'Unknown';
            if ($milk->sourceBatches->isNotEmpty()) {
                $milkType = 'Pasteurized';
            } elseif ($milk->sourceDonations->isNotEmpty()) {
                $milkType = 'Unpasteurized';
            }

            $dispenseTime = $milk->time_dispensed;
            if (!$dispenseTime && $milk->breastmilkRequest && $milk->breastmilkRequest->dispensed_at) {
                $dispenseTime = $milk->breastmilkRequest->dispensed_at;
            }

            return [
                'guardian' => $guardian ? $this->formatFullName($guardian) : '-',
                'infant' => $recipient ? $this->formatFullName($recipient) : '-',
                'donor_or_batch' => $donorLabels->isNotEmpty() ? $donorLabels->implode(', ') : '-',
                'volume_dispensed' => (float) ($milk->volume_dispensed ?? 0),
                'milk_type' => $milkType,
                'dispensed_date' => $this->formatDateValue($milk->date_dispensed),
                'dispensed_time' => $this->formatTimeValue($dispenseTime),
            ];
        });

        return [
            'records' => $records,
            'total_volume' => $records->sum('volume_dispensed'),
        ];
    }

    protected function buildDonationData(int $year, int $month): array
    {
        $donations = Donation::with('user')
            ->whereIn('status', ['success_walk_in', 'success_home_collection'])
            ->where(function ($query) use ($year, $month) {
                $query->where(function ($dateQuery) use ($year, $month) {
                    $dateQuery->whereYear('donation_date', $year)
                        ->whereMonth('donation_date', $month);
                })
                    ->orWhere(function ($pickupQuery) use ($year, $month) {
                        $pickupQuery->whereYear('scheduled_pickup_date', $year)
                            ->whereMonth('scheduled_pickup_date', $month);
                    });
            })
            ->orderByRaw('COALESCE(donation_date, scheduled_pickup_date) asc')
            ->orderByRaw('COALESCE(donation_time, scheduled_pickup_time) asc')
            ->get();

        $records = $donations->map(function (Donation $donation) {
            $bagVolumes = collect($donation->individual_bag_volumes ?? [])
                ->filter(fn ($volume) => $volume !== null && $volume !== '')
                ->map(fn ($volume) => number_format((float) $volume, 0) . ' ml')
                ->implode(', ');

            return [
                'donation_type' => $donation->donation_method === 'walk_in' ? 'Walk-in' : 'Home Collection',
                'name' => $donation->user ? $this->formatFullName($donation->user) : '-',
                'address' => $donation->user->address ?? '-',
                'number_of_bags' => $donation->number_of_bags ?? 0,
                'volume_per_bag' => $bagVolumes ?: '-',
                'total_volume' => (float) ($donation->total_volume ?? 0),
                'date' => $this->formatDateValue($donation->donation_date ?? $donation->scheduled_pickup_date),
                'time' => $this->formatTimeValue($donation->donation_time ?? $donation->scheduled_pickup_time),
            ];
        });

        return [
            'records' => $records,
            'total_volume' => $records->sum('total_volume'),
        ];
    }

    protected function buildInventoryData(int $year, int $month): array
    {
        $unpasteurized = Donation::with('user')
            ->whereIn('status', ['success_walk_in', 'success_home_collection'])
            ->where('pasteurization_status', 'unpasteurized')
            ->where(function ($query) use ($year, $month) {
                $query->where(function ($sub) use ($year, $month) {
                    $sub->whereYear('added_to_inventory_at', $year)
                        ->whereMonth('added_to_inventory_at', $month);
                })
                    ->orWhere(function ($sub) use ($year, $month) {
                        $sub->whereYear('donation_date', $year)
                            ->whereMonth('donation_date', $month);
                    })
                    ->orWhere(function ($sub) use ($year, $month) {
                        $sub->whereYear('scheduled_pickup_date', $year)
                            ->whereMonth('scheduled_pickup_date', $month);
                    });
            })
            ->orderByRaw('COALESCE(added_to_inventory_at, donation_date, scheduled_pickup_date) asc')
            ->get();

        $pasteurized = PasteurizationBatch::with('donations')
            ->whereYear('date_pasteurized', $year)
            ->whereMonth('date_pasteurized', $month)
            ->orderBy('date_pasteurized')
            ->orderBy('time_pasteurized')
            ->get();

        $dispensed = DispensedMilk::with(['guardian', 'recipient', 'sourceDonations.user', 'sourceBatches'])
            ->whereYear('date_dispensed', $year)
            ->whereMonth('date_dispensed', $month)
            ->orderBy('date_dispensed')
            ->orderBy('time_dispensed')
            ->get();

        $unpasteurizedRows = $unpasteurized->map(function (Donation $donation) {
            $bagVolumes = collect($donation->individual_bag_volumes ?? [])
                ->filter(fn ($volume) => $volume !== null && $volume !== '')
                ->map(fn ($volume) => number_format((float) $volume, 0) . 'ml')
                ->implode(', ');

            return [
                'donor' => $donation->user ? $this->formatFullName($donation->user) : '-',
                'type' => $donation->donation_method === 'walk_in' ? 'Walk-in' : 'Home Collection',
                'bags' => $donation->number_of_bags ?? 0,
                'volume_per_bag' => $bagVolumes ?: '-',
                'total' => (float) ($donation->total_volume ?? 0),
                'available' => (float) ($donation->available_volume ?? $donation->total_volume ?? 0),
                'date' => $this->formatDateValue($donation->donation_date ?? $donation->scheduled_pickup_date),
                'time' => $this->formatTimeValue($donation->donation_time ?? $donation->scheduled_pickup_time),
            ];
        });

        $pasteurizedRows = $pasteurized->map(function (PasteurizationBatch $batch) {
            return [
                'batch' => $batch->batch_number ?? 'Batch ' . $batch->batch_id,
                'total' => (float) ($batch->total_volume ?? 0),
                'available' => (float) ($batch->available_volume ?? $batch->total_volume ?? 0),
                'date' => $this->formatDateValue($batch->date_pasteurized),
                'time' => $this->formatTimeValue($batch->time_pasteurized),
                'count' => $batch->donations->count(),
            ];
        });

        $dispensedRows = $dispensed->map(function (DispensedMilk $milk) {
            $guardian = $milk->guardian ? $this->formatFullName($milk->guardian) : '-';
            $recipient = $milk->recipient ? $this->formatFullName($milk->recipient) : '-';

            $donorLabels = $milk->sourceDonations->map(function (Donation $donation) {
                $user = $donation->user;
                $name = $user ? $this->formatFullName($user) : null;
                if (!$name && !empty($donation->donor_name)) {
                    $name = $donation->donor_name;
                }
                return $name ?: 'Donation #' . $donation->breastmilk_donation_id;
            });

            if ($donorLabels->isEmpty()) {
                $donorLabels = $milk->sourceBatches->map(function (PasteurizationBatch $batch) {
                    return $batch->batch_number ?? 'Batch ' . $batch->batch_id;
                });
            }

            return [
                'guardian' => $guardian,
                'recipient' => $recipient,
                'source' => $donorLabels->isNotEmpty() ? $donorLabels->implode(', ') : '-',
                'volume' => (float) ($milk->volume_dispensed ?? 0),
                'date' => $this->formatDateValue($milk->date_dispensed),
                'time' => $this->formatTimeValue($milk->time_dispensed),
            ];
        });

        return [
            'sections' => [
                [
                    'label' => 'Unpasteurized Breastmilk',
                    'type' => 'unpasteurized',
                    'rows' => $unpasteurizedRows,
                    'total' => $unpasteurizedRows->sum('available'),
                ],
                [
                    'label' => 'Pasteurized Breastmilk',
                    'type' => 'pasteurized',
                    'rows' => $pasteurizedRows,
                    'total' => $pasteurizedRows->sum('available'),
                ],
                [
                    'label' => 'Dispensed Breastmilk',
                    'type' => 'dispensed',
                    'rows' => $dispensedRows,
                    'total' => $dispensedRows->sum('volume'),
                ],
            ],
        ];
    }

    protected function buildFilename(string $title, Carbon $period): string
    {
        $sanitized = str_replace(' ', '_', ucwords($title));

        return sprintf('%s_%s.pdf', $sanitized, $period->format('F_Y'));
    }

    protected function formatFullName($model): string
    {
        if (!$model) {
            return '-';
        }

        $parts = collect([
            $model->first_name ?? null,
            $model->middle_name ?? null,
            $model->last_name ?? null,
        ])->filter();

        if ($parts->isEmpty()) {
            return '-';
        }

        return $parts->implode(' ');
    }

    protected function formatDateValue($value, string $format = 'M d, Y'): string
    {
        if (!$value) {
            return '-';
        }

        $date = $value instanceof Carbon ? $value : Carbon::parse($value);

        return $date->format($format);
    }

    protected function formatTimeValue($value, string $format = 'g:i A'): string
    {
        if (!$value) {
            return '-';
        }

        $time = $value instanceof Carbon ? $value : Carbon::parse($value);

        return $time->format($format);
    }
}
