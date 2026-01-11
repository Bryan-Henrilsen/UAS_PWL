<!DOCTYPE html>
<html>
<head>
    <title>Laporan Barang Keluar</title>
    <style>
        body { font-family: sans-serif; font-size: 9pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 5px; vertical-align: top; }
        th { background-color: #eee; text-align: center; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-danger { color: #d9534f; font-weight: bold; font-size: 0.9em; }
        ul { margin: 0; padding-left: 15px; }
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
                <th width="10%">Tanggal</th>
                <th width="20%">Tujuan</th>
                <th width="12%">Sales</th>
                <th>Detail Item (Harga & Diskon)</th>
                <th width="20%">Rincian Biaya</th>
            </tr>
        </thead>
        <tbody>
            @foreach($outbounds as $out)
            <tr>
                <td class="text-center">{{ $out->outbound_date->format('d/m/Y') }}</td>
                <td>
                    {{ $out->delivery_data }}<br>
                    <small style="color: #666;">Ref: #{{ $out->id }}</small>
                </td>
                <td>{{ $out->requester->name }}</td>
                <td>
                    <ul style="list-style-type: none; padding-left: 0;">
                    @foreach($out->details as $d)
                        <li style="margin-bottom: 4px;">
                            <b>{{ $d->variant->product->name }}</b> ({{ $d->variant->size }}) <br>
                            <small>
                                {{ $d->qty }} pcs x Rp {{ number_format($d->unit_price, 0, ',', '.') }}
                                @if($d->discount_percent > 0)
                                    <span class="text-danger">(Disc {{ $d->discount_percent }}%)</span>
                                @endif
                            </small>
                        </li>
                    @endforeach
                    </ul>
                </td>
                <td class="text-right">
                    <table style="width: 100%; border: none; margin: 0;">
                        <tr>
                            <td style="border: none; padding: 0;">Subtotal:</td>
                            <td style="border: none; padding: 0;" class="text-right">Rp {{ number_format($out->total_amount, 0, ',', '.') }}</td>
                        </tr>
                        @if($out->tax_amount > 0)
                        <tr>
                            <td style="border: none; padding: 0; color: #666;">Pajak ({{ $out->tax_rate }}%):</td>
                            <td style="border: none; padding: 0; color: #666;" class="text-right">Rp {{ number_format($out->tax_amount, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="2" style="border: none; border-top: 1px solid #ccc; padding-top: 4px; margin-top: 4px;">
                                <b>Total: Rp {{ number_format($out->grand_total, 0, ',', '.') }}</b>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">TOTAL PENJUALAN (Omzet)</th>
                <th class="text-right">Rp {{ number_format($totalGrand, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>