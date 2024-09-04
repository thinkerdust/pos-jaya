<table>
    <thead>
        <tr>
            <th colspan="8" style="font-weight: bold; text-align: center;font-size:16px;">{{ $company_name }}</th>
        </tr>
        <tr>
            <th colspan="8" style="font-weight: bold; text-align: center;font-size:16px;">Laporan Piutang Pelanggan</th>
        </tr>
        <tr>
            <th colspan="8" style="font-weight: bold; text-align: center;font-size:16px;">{{ $min }} - {{ $max }}</th>
        </tr>
        <tr>
            <th colspan="8" style="font-weight: bold; text-align: center;font-size:16px;">dalam IDR</th>
        </tr>
        <tr>
            <th style="font-weight: bold;">Pelanggan</th>
            <th style="font-weight: bold;">Tanggal</th>
            <th style="font-weight: bold;">Transaksi</th>
            <th style="font-weight: bold;">Nomor</th>
            <th style="font-weight: bold;">Jatuh Tempo</th>
            <th style="font-weight: bold;">Jumlah</th>
            <th style="font-weight: bold;">Pemotongan</th>
            <th style="font-weight: bold;">Sisa Piutang</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $jumlah = 0;
            $pemotongan = 0;
            $sisa_piutang = 0;
        @endphp
        @foreach($data as $row)
        @php 
            $jumlah += $row->jumlah;
            $pemotongan += $row->pemotongan;
            $sisa_piutang += $row->sisa_piutang;
        @endphp 
        <tr>
            <td>{{ $row->nama_pelanggan }}</td>
            <td>{{ $row->tanggal }}</td>
            <td>Sales Invoice</td>
            <td>{{ $row->invoice_number }}</td>
            <td>{{ $row->jatuh_tempo }}</td>
            <td>{{ $row->jumlah }}</td>
            <td>{{ $row->pemotongan }}</td>
            <td>{{ $row->sisa_piutang }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" style="font-weight: bold;text-align:center">TOTAL</td>
            <td style="font-weight: bold;">{{ $jumlah }}</td>
            <td style="font-weight: bold;">{{ $pemotongan }}</td>
            <td style="font-weight: bold;">{{ $sisa_piutang }}</td>
        </tr>
    </tfoot>
</table>