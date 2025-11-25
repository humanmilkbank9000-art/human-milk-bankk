@php
    $rows = collect($rows ?? []);
    // Grand total is the sum of the per-row `total` values (Total (ml)).
    $total = $total ?? $rows->sum('total');
@endphp

<table class="report-table">
    <colgroup>
        <col style="width:5%">
        <col style="width:22%">
        <col style="width:12%">
        <col style="width:8%">
        <col style="width:18%">
        <col style="width:12%">
        <col style="width:13%">
        <col style="width:10%">
    </colgroup>
    <thead>
        <tr>
            <th style="width: 50px;">No</th>
            <th>Donor</th>
            <th>Type</th>
            <th style="width: 60px;" title="Count of Bag">Bags</th>
            <th>Volume/Bag</th>
            <th style="width: 100px;">Total (ml)</th>
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
                <td class="text-end">
                    @php
                        $vol = (float) ($row['total'] ?? 0);
                        echo $vol == (int) $vol ? (int) $vol : rtrim(rtrim(number_format($vol, 2, '.', ''), '0'), '.');
                    @endphp
                </td>
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
            <td colspan="5"><strong>Grand Total</strong></td>
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