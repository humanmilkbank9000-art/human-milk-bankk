@php
    $rows = collect($rows ?? []);
    $total = $total ?? $rows->sum('available');
@endphp

<table class="report-table">
    <colgroup>
        <col style="width:5%">
        <col style="width:28%">
        <col style="width:12%">
        <col style="width:12%">
        <col style="width:16%">
        <col style="width:12%">
        <col style="width:15%">
    </colgroup>
    <thead>
        <tr>
            <th style="width: 50px;">No</th>
            <th>Batch Number</th>
            <th style="width: 120px;">Total (ml)</th>
            <th style="width: 120px;" title="Available (ml)">Available</th>
            <th style="width: 120px;">Date</th>
            <th style="width: 100px;">Time</th>
            <th style="width: 80px;">Donations Count</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($rows->values() as $index => $row)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $row['batch'] ?? '-' }}</td>
                <td class="text-end">
                    @php
                        $vol = (float) ($row['total'] ?? 0);
                        echo $vol == (int) $vol ? (int) $vol : rtrim(rtrim(number_format($vol, 2, '.', ''), '0'), '.');
                    @endphp
                </td>
                <td class="text-end">
                    @php
                        $vol = (float) ($row['available'] ?? 0);
                        echo $vol == (int) $vol ? (int) $vol : rtrim(rtrim(number_format($vol, 2, '.', ''), '0'), '.');
                    @endphp
                </td>
                <td style="text-align: center;">{{ $row['date'] ?? '-' }}</td>
                <td style="text-align: center;">{{ $row['time'] ?? '-' }}</td>
                <td style="text-align: center;">{{ $row['count'] ?? 0 }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" style="text-align:center;">No pasteurized batches for this period.</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3"><strong>Grand Total Available</strong></td>
            <td class="text-end">
                <strong>
                    @php
                        $vol = (float) $total;
                        echo $vol == (int) $vol ? (int) $vol : rtrim(rtrim(number_format($vol, 2, '.', ''), '0'), '.');
                    @endphp
                </strong>
            </td>
            <td colspan="3"></td>
        </tr>
    </tfoot>
</table>