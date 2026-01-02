<!DOCTYPE html>
<html>
<head>
    <title>Laporan Barang Keluar</title>
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
        <h2>Laporan Barang Keluar (Outbound)</h2>
        <p>Periode: {{ date('d M Y', strtotime($startDate)) }} s/d {{ date('d M Y', strtotime($endDate)) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Customer / Tujuan</th>
                <th>Sales / Staff</th>
                <th>Item Barang</th>
                <th>Total Penjualan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($outbounds as $out)
            <tr>
                <td>{{ $out->outbound_date->format('d/m/Y') }}</td>
                <td>{{ $out->delivery_data }}</td>
                <td>{{ $out->requester->name }}</td>
                <td>
                    <ul style="margin: 0; padding-left: 15px;">
                    @foreach($out->details as $d)
                        <li>{{ $d->variant->product->name }} ({{ $d->variant->size }}) : <b>{{ $d->qty }} pcs</b></li>
                    @endforeach
                    </ul>
                </td>
                <td style="text-align: right;">Rp {{ number_format($out->grand_total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" style="text-align: right;">TOTAL PENJUALAN PERIODE INI</th>
                <th style="text-align: right;">Rp {{ number_format($totalGrand, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>