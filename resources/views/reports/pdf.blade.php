<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; padding: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Keuangan (Buku Kas Umum)</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Keterangan / Rincian</th>
                <th>Pemasukan (Rp)</th>
                <th>Pengeluaran (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $in = 0; $out = 0; @endphp
            @foreach($transactions as $t)
                @php
                    if($t->tipe == 'masuk') $in += $t->nominal;
                    else $out += $t->nominal;
                @endphp
                <tr>
                    <td class="text-center">{{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $t->deskripsi }}</td>
                    <td class="text-right">{{ $t->tipe == 'masuk' ? number_format($t->nominal, 0, ',', '.') : '-' }}</td>
                    <td class="text-right">{{ $t->tipe == 'keluar' ? number_format($t->nominal, 0, ',', '.') : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-right">TOTAL</th>
                <th class="text-right">{{ number_format($in, 0, ',', '.') }}</th>
                <th class="text-right">{{ number_format($out, 0, ',', '.') }}</th>
            </tr>
            <tr>
                <th colspan="2" class="text-right">SALDO PERIODE INI</th>
                <th colspan="2" class="text-center">{{ number_format($in - $out, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>