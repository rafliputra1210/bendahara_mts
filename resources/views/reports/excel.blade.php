<table>
    <thead>
        <tr>
            <th colspan="4" style="font-weight: bold; font-size: 14px; text-align: center;">LAPORAN KEUANGAN (BUKU KAS UMUM)</th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center;">Periode: {{ $startDate }} s/d {{ $endDate }}</th>
        </tr>
        <tr>
            <th>Tanggal</th>
            <th>Keterangan</th>
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
                <td>{{ $t->tanggal }}</td>
                <td>{{ $t->deskripsi }}</td>
                <td>{{ $t->tipe == 'masuk' ? $t->nominal : '' }}</td>
                <td>{{ $t->tipe == 'keluar' ? $t->nominal : '' }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="2" style="font-weight: bold; text-align: right;">TOTAL</td>
            <td style="font-weight: bold;">{{ $in }}</td>
            <td style="font-weight: bold;">{{ $out }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold; text-align: right;">SALDO</td>
            <td colspan="2" style="font-weight: bold; text-align: center;">{{ $in - $out }}</td>
        </tr>
    </tbody>
</table>
