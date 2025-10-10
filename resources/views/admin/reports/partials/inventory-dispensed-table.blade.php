@php
    $rows = collect($rows ?? []);
    $total = $total ?? $rows->sum('volume');
@endphp

<table class="report-table">
    <thead>
        <tr>
            <th style="width: 50px;">No</th>
            <th>Guardian</th>
            <th>Recipient</th>
            <th>Source (Donor/Batch)</th>
            <th style="width: 120px;">Volume (ml)</th>
            <th style="width: 120px;">Date</th>
            <th style="width: 100px;">Time</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($rows->values() as $index => $row)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $row['guardian'] ?? '-' }}</td>
                <td>{{ $row['recipient'] ?? '-' }}</td>
                <td style="font-size: 10px;">{{ $row['source'] ?? '-' }}</td>
                <td class="text-end">{{ number_format((float) ($row['volume'] ?? 0), 2) }}</td>
                <td style="text-align: center;">{{ $row['date'] ?? '-' }}</td>
                <td style="text-align: center;">{{ $row['time'] ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" style="text-align:center;">No dispensed records for this period.</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4"><strong>Grand Total Dispensed</strong></td>
            <td class="text-end"><strong>{{ number_format((float) $total, 2) }}</strong></td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>