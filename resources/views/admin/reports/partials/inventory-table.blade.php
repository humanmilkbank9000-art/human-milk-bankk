@php
    $rows = collect($rows ?? []);
    $total = $total ?? $rows->sum('volume');
@endphp

<table class="report-table">
    <thead>
        <tr>
            <th style="width: 50px;">No</th>
            <th>Milk Batch Number / Reference</th>
            <th>Volume (ml)</th>
            <th>Date Stored</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($rows->values() as $index => $row)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $row['identifier'] ?? '-' }}</td>
                <td class="text-end">{{ number_format((float) ($row['volume'] ?? 0), 2) }}</td>
                <td>{{ $row['date'] ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align:center;">No records in this category.</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3"><strong>Grand Total</strong></td>
            <td class="text-end"><strong>{{ number_format((float) $total, 2) }}</strong></td>
        </tr>
    </tfoot>
</table>