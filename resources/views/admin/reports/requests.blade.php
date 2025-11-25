@php
    $records = collect($records ?? []);
    $totalVolume = $total_volume ?? $records->sum('volume_dispensed');
@endphp

<table class="report-table">
    <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th style="width: 18%;">Guardian</th>
            <th style="width: 18%;">Infant</th>
            <th style="width: 25%;">Batch</th>
            <th style="width: 14%;">Dispensed Date</th>
            <th style="width: 10%;">Dispensed Time</th>
            <th style="width: 10%;">Total Volume (ml)</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($records->values() as $index => $row)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $row['guardian'] ?? '-' }}</td>
                <td>{{ $row['infant'] ?? '-' }}</td>
                <td style="word-wrap: break-word;">{{ $row['donor_or_batch'] ?? '-' }}</td>
                <td>{{ $row['dispensed_date'] ?? '-' }}</td>
                <td>{{ $row['dispensed_time'] ?? '-' }}</td>
                <td class="text-end">
                    @php
                        $vol = (float) ($row['volume_dispensed'] ?? 0);
                        echo $vol == (int) $vol ? (int) $vol : rtrim(rtrim(number_format($vol, 2, '.', ''), '0'), '.');
                    @endphp
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" style="text-align:center;">No dispensed requests for this period.</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6"><strong>Grand Total</strong></td>
            <td class="text-end">
                <strong>
                    @php
                        $vol = (float) $totalVolume;
                        echo $vol == (int) $vol ? (int) $vol : rtrim(rtrim(number_format($vol, 2, '.', ''), '0'), '.');
                    @endphp
                </strong>
            </td>
        </tr>
    </tfoot>
</table>