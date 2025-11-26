<?php

namespace App\Services;

use App\Models\DispensedMilk;
use App\Models\Donation;
use App\Models\PasteurizationBatch;
use Carbon\Carbon;

class ReportService
{
    public function buildReportPayload(string $type, int $year, int $month): array
    {
        $normalizedType = strtolower($type);
        $timezone = config('app.timezone', 'UTC');
        $now = Carbon::now($timezone);

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
                throw new \InvalidArgumentException('Report type not found.');
        }
    }

    public function buildRequestData(int $year, int $month): array
    {
        // Get all breastmilk requests for the given month/year
        // Eager-load nested dispensedMilk relations to avoid N+1 when accessing sources
        // Only include dispensed requests (exclude pending, approved, and declined)
        $requests = \App\Models\BreastmilkRequest::with(['user', 'infant', 'dispensedMilk.sourceDonations.user', 'dispensedMilk.sourceBatches'])
            ->whereYear('request_date', $year)
            ->whereMonth('request_date', $month)
            ->where('status', 'dispensed')
            ->orderBy('request_date', 'desc')
            ->orderBy('request_time', 'desc')
            ->get();

        // Count by status (only dispensed are included in the query)
        $total = $requests->count();
        // All requests are dispensed
        $approved = $total;
        $declined = 0; // Declined requests are excluded from the report
        // Total dispensed volume for the period (sum of volume_dispensed)
        $totalDispensedVolume = $requests->sum(function ($r) {
            return (float) ($r->volume_dispensed ?? 0);
        });

        // Map records for display
        $records = $requests->map(function ($request) {
            $guardian = $request->user ? $this->formatFullName($request->user) : '-';
            $infant = $request->infant ? $this->formatFullName($request->infant) : '-';

            // Dispensing related details (if dispensed)
            $donorOrBatch = '-';
            $milkType = '-';
            $dispensedDate = '-';
            $dispensedTime = '-';

            if (!empty($request->dispensedMilk)) {
                $dm = $request->dispensedMilk;
                // use DispensedMilk accessors/helpers when available
                $donorOrBatch = method_exists($dm, 'getSourceNameAttribute') ? $dm->source_name : ($dm->getSourceNameAttribute() ?? '-');
                $milkType = $dm->milk_type ? ucfirst($dm->milk_type) : '-';
                $dispensedDate = $dm->date_dispensed ? $this->formatDateValue($dm->date_dispensed) : ($dm->getFormattedDateAttribute() ?? '-');
                $dispensedTime = $dm->time_dispensed ? $this->formatTimeValue($dm->time_dispensed) : ($dm->getFormattedTimeAttribute() ?? '-');
            }

            return [
                'guardian' => $guardian,
                'infant' => $infant,
                'volume_requested' => (float) ($request->volume_requested ?? 0),
                'volume_dispensed' => (float) ($request->volume_dispensed ?? 0),
                'status' => ucfirst($request->status ?? 'pending'),
                'request_date' => $this->formatDateValue($request->request_date),
                'request_time' => $this->formatTimeValue($request->request_time),
                'approved_at' => $request->approved_at ? $this->formatDateValue($request->approved_at) : '-',
                'declined_at' => $request->declined_at ? $this->formatDateValue($request->declined_at) : '-',
                'dispensed_at' => $request->dispensed_at ? $this->formatDateValue($request->dispensed_at) : '-',
                // Fields used by the requests report preview
                'donor_or_batch' => $donorOrBatch,
                'milk_type' => $milkType,
                'dispensed_date' => $dispensedDate,
                'dispensed_time' => $dispensedTime,
            ];
        });

        return [
            'records' => $records,
            'total' => $total,
            'approved' => $approved,
            'declined' => $declined,
            // expose total dispensed volume (ml) instead of pending count
            'dispensed_volume' => $totalDispensedVolume,
        ];
    }

    public function buildDonationData(int $year, int $month): array
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

    public function buildInventoryData(int $year, int $month): array
    {
        $unpasteurized = Donation::with('user')
            ->whereIn('status', ['success_walk_in', 'success_home_collection'])
            ->where('pasteurization_status', 'unpasteurized')
            ->where(function ($query) {
                $query->where('available_volume', '>', 0)
                    ->orWhereRaw('(total_volume - COALESCE(dispensed_volume, 0)) > 0');
            })
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
            ->where(function ($query) {
                $query->where('available_volume', '>', 0)
                    ->orWhereRaw('COALESCE(available_volume, total_volume) > 0');
            })
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

            $availableVolume = $donation->available_volume !== null 
                ? (float) $donation->available_volume 
                : (float) (($donation->total_volume ?? 0) - ($donation->dispensed_volume ?? 0));

            return [
                'donor' => $donation->user ? $this->formatFullName($donation->user) : '-',
                'type' => $donation->donation_method === 'walk_in' ? 'Walk-in' : 'Home Collection',
                'bags' => $donation->number_of_bags ?? 0,
                'volume_per_bag' => $bagVolumes ?: '-',
                'total' => (float) ($donation->total_volume ?? 0),
                'available' => $availableVolume,
                'date' => $this->formatDateValue($donation->donation_date ?? $donation->scheduled_pickup_date),
                'time' => $this->formatTimeValue($donation->donation_time ?? $donation->scheduled_pickup_time),
            ];
        });

        $pasteurizedRows = $pasteurized->map(function ($batch) {
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
                $donorLabels = $milk->sourceBatches->map(function ($batch) {
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
