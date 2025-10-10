@php
    $rows = collect($rows ?? []);
    $total = $total ?? $rows->sum('available');
@endphp

<table class="report-table">
    <thead>
        <tr>
            <th style="width: 50px;">No</th>
            <th>Donor</th>
            <th>Type</th>
            <th style="width: 60px;">Bags</th>
            <th>Volume/Bag</th>
            <th style="width: 100px;">Total (ml)</th>
            <th style="width: 100px;">Available (ml)</th>
            <th style="width: 120px;">Date</th>
            <th style="width: 100px;">Time</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($rows->values() as $index => $row)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $row['donor'] ?? '-' }}</td>
                <td style="text-align: center;">{{ $row['type'] ?? '-' }}</td>
                <td style="text-align: center;">{{ $row['bags'] ?? 0 }}</td>
                <td style="font-size: 10px;">{{ $row['volume_per_bag'] ?? '-' }}</td>
                <td class="text-end">{{ number_format((float) ($row['total'] ?? 0), 2) }}</td>
                <td class="text-end">{{ number_format((float) ($row['available'] ?? 0), 2) }}</td>
                <td style="text-align: center;">{{ $row['date'] ?? '-' }}</td>
                <td style="text-align: center;">{{ $row['time'] ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="text-align:center;">No unpasteurized breastmilk for this period.</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6"><strong>Grand Total Available</strong></td>
            <td class="text-end"><strong>{{ number_format((float) $total, 2) }}</strong></td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>