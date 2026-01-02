<!DOCTYPE html>
<html>
<head>
    <title>Laporan Barang Masuk</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 4px; }
        th { background-color: #ddd; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Barang Masuk (Inbound)</h2>
        <p>Periode: {{ date('d M Y', strtotime($startDate)) }} s/d {{ date('d M Y', strtotime($endDate)) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>No. Transaksi</th>
                <th>Staff</th>
                <th>Item Barang (Varian : Qty)</th>
                <th>Total Nominal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inbounds as $in)
            <tr>
                <td>{{ $in->inbound_date->format('d/m/Y') }}</td>
                <td>ID: {{ $in->id }}</td>
                <td>{{ $in->requester->name }}</td>
                <td>
                    <ul style="margin: 0; padding-left: 15px;">
                    @foreach($in->details as $d)
                        <li>{{ $d->variant->product->name }} ({{ $d->variant->size }}) : <b>{{ $d->qty }} pcs</b></li>
                    @endforeach
                    </ul>
                </td>
                <td style="text-align: right;">Rp {{ number_format($in->total_amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" style="text-align: right;">GRAND TOTAL PERIODE INI</th>
                <th style="text-align: right;">Rp {{ number_format($totalNominal, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>