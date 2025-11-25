@php
    $rows = collect($rows ?? []);
    $total = $total ?? $rows->sum('volume');
@endphp

<table class="report-table">
    <colgroup>
        <col style="width:5%">
        <col style="width:18%">
        <col style="width:18%">
        <col style="width:28%">
        <col style="width:12%">
        <col style="width:12%">
        <col style="width:7%">
    </colgroup>
    <thead>
        <tr>
            <th style="width: 50px;">No</th>
            <th>Guardian</th>
            <th>Recipient</th>
            <th style="white-space: normal;" title="Source (Donor/Batch)">Source (Donor/Batch)</th>
            <th style="width: 120px; white-space: normal;" title="Volume (ml)">Volume (ml)</th>
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
                <td style="font-size: 10px; white-space: normal; word-break: break-word;">{{ $row['source'] ?? '-' }}</td>
                <td class="text-end">
                    @php
                        $vol = (float) ($row['volume'] ?? 0);
                        echo $vol == (int) $vol ? (int) $vol : rtrim(rtrim(number_format($vol, 2, '.', ''), '0'), '.');
                    @endphp
                </td>
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
            <td class="text-end">
                <strong>
                    @php
                        $vol = (float) $total;
                        echo $vol == (int) $vol ? (int) $vol : rtrim(rtrim(number_format($vol, 2, '.', ''), '0'), '.');
                    @endphp
                </strong>
            </td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>