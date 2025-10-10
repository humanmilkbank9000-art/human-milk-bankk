@php
    $sections = collect($sections ?? []);
@endphp

@if ($sections->isEmpty())
    <p>No inventory activity for this period.</p>
@else
    @foreach ($sections as $section)
        <h3 style="margin: 24px 0 12px; font-size: 16px; color: #0f172a;">{{ $section['label'] ?? 'Inventory' }}</h3>

        @if (($section['type'] ?? '') === 'unpasteurized')
            @include('admin.reports.partials.inventory-unpasteurized-table', [
                'rows' => collect($section['rows'] ?? []),
                'total' => $section['total'] ?? 0,
            ])
        @elseif (($section['type'] ?? '') === 'pasteurized')
                    @include('admin.reports.partials.inventory-pasteurized-table', [
                        'rows' => collect($section['rows'] ?? []),
                        'total' => $section['total'] ?? 0,
                    ])
                @elseif (($section['type'] ?? '') === 'dispensed')
                    @include('admin.reports.partials.inventory-dispensed-table', [
                        'rows' => collect($section['rows'] ?? []),
                        'total' => $section['total'] ?? 0,
                    ])
                @else
                    @include('admin.reports.partials.inventory-table', [
                        'rows' => collect($section['rows'] ?? []),
                        'total' => $section['total'] ?? 0,
                    ])
                @endif
    @endforeach
@endif
