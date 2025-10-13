@php
    $records = collect($records ?? []);
    $totalVolume = $total_volume ?? $records->sum('total_volume');
@endphp

<table class="report-table">
    <thead>
        <tr>
            <th style="width: 50px;">No.</th>
            <th>Name</th>
            <th>Donation type</th>
            <th>Address</th>
            <th>Number of Bags</th>
            <th>Volume per Bag</th>
            <th>Date</th>
            <th>Time</th>
            <th>Total Volume</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($records->values() as $index => $row)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $row['name'] ?? '' }}</td>
                <td>{{ $row['donation_type'] ?? '' }}</td>
                <td>{{ $row['address'] ?? '' }}</td>
                <td style="text-align: center;">{{ $row['number_of_bags'] ?? '' }}</td>
                <td style="text-align: center;">{{ $row['volume_per_bag'] ?? '' }}</td>
                <td style="text-align: center;">{{ $row['date'] ?? '' }}</td>
                <td style="text-align: center;">{{ $row['time'] ?? '' }}</td>
                <td style="text-align: center;">
                    @php
                        $vol = (float) $row['total_volume'];
                        echo $vol ? ($vol == (int) $vol ? (int) $vol : rtrim(rtrim(number_format($vol, 2, '.', ''), '0'), '.')) : '';
                    @endphp
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="text-align:center;">No donation records for this period.</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="8" style="text-align: right; padding-right: 10px;"><strong>Grand total</strong></td>
            <td style="text-align: center;">
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