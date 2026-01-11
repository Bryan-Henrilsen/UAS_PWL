<!DOCTYPE html>
<html>
<head>
    <title>Laporan Barang Masuk</title>
    <style>
        body { font-family: sans-serif; font-size: 9pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 5px; vertical-align: top; }
        th { background-color: #eee; text-align: center; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        ul { margin: 0; padding-left: 15px; }
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
                <th width="12%">Tanggal</th>
                <th width="12%">No. Transaksi</th>
                <th width="15%">Staff</th>
                <th>Detail Item (Harga Beli)</th>
                <th width="18%">Total Nominal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inbounds as $in)
            <tr>
                <td class="text-center">{{ $in->inbound_date->format('d/m/Y') }}</td>
                <td class="text-center">#{{ $in->id }}</td>
                <td>{{ $in->requester->name }}</td>
                <td>
                    <ul style="list-style-type: none; padding-left: 0;">
                    @foreach($in->details as $d)
                        <li style="margin-bottom: 4px;">
                            <b>{{ $d->variant->product->name }}</b> ({{ $d->variant->size }}) <br>
                            <small style="color: #555;">
                                {{ $d->qty }} pcs x Rp {{ number_format($d->unit_price, 0, ',', '.') }} 
                                = <b>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</b>
                            </small>
                        </li>
                    @endforeach
                    </ul>
                </td>
                <td class="text-right">Rp {{ number_format($in->total_amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">GRAND TOTAL PERIODE INI</th>
                <th class="text-right">Rp {{ number_format($totalNominal, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>