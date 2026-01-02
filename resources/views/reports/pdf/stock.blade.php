<!DOCTYPE html>
<html>
<head>
    <title>Laporan Stok & Aset</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #eee; }
        .text-right { text-align: right; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Posisi Stok & Aset</h2>
        <p>Tanggal Cetak: {{ date('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Varian</th>
                <th class="text-right">Harga Satuan</th>
                <th class="text-right">Stok</th>
                <th class="text-right">Nilai Aset</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stocks as $key => $s)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $s->product->name }}</td>
                <td>{{ $s->color }} - {{ $s->size }}</td>
                <td class="text-right">Rp {{ number_format($s->price, 0, ',', '.') }}</td>
                <td class="text-right">{{ $s->stock_qty }}</td>
                <td class="text-right">Rp {{ number_format($s->stock_qty * $s->price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-right">TOTAL NILAI ASET</th>
                <th class="text-right">Rp {{ number_format($totalAsset, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>