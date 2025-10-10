@php
    $records = collect($records ?? []);
    $totalVolume = $total_volume ?? $records->sum('volume_dispensed');
@endphp

<table class="report-table">
    <thead>
        <tr>
            <th style="width: 50px;">No</th>
            <th>Guardian</th>
            <th>Infant</th>
            <th>Donor / Batch</th>
            <th>Milk Type</th>
            <th>Dispensed Date</th>
            <th>Dispensed Time</th>
            <th style="width: 140px;">Total Volume (ml)</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($records->values() as $index => $row)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $row['guardian'] ?? '-' }}</td>
                <td>{{ $row['infant'] ?? '-' }}</td>
                <td>{{ $row['donor_or_batch'] ?? '-' }}</td>
                <td>{{ $row['milk_type'] ?? '-' }}</td>
                <td>{{ $row['dispensed_date'] ?? '-' }}</td>
                <td>{{ $row['dispensed_time'] ?? '-' }}</td>
                <td class="text-end">{{ number_format((float) ($row['volume_dispensed'] ?? 0), 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="text-align:center;">No dispensed requests for this period.</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7"><strong>Grand Total</strong></td>
            <td class="text-end"><strong>{{ number_format((float) $totalVolume, 2) }}</strong></td>
        </tr>
    </tfoot>
</table>