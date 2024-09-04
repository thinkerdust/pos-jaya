<table>
    <thead>
        <tr>
            <th colspan="10" style="font-weight: bold; text-align: center;font-size:16px;">{{ $company_name }}</th>
        </tr>
        <tr>
            <th colspan="10" style="font-weight: bold; text-align: center;font-size:16px;">Penjualan per Pelanggan</th>
        </tr>
        <tr>
            <th colspan="10" style="font-weight: bold; text-align: center;font-size:16px;">{{ $min }} - {{ $max }}</th>
        </tr>
        <tr>
            <th colspan="10" style="font-weight: bold; text-align: center;font-size:16px;">dalam IDR</th>
        </tr>
        <tr>
            <th style="font-weight: bold;">Pelanggan</th>
            <th style="font-weight: bold;">Tanggal</th>
            <th style="font-weight: bold;">Transaksi</th>
            <th style="font-weight: bold;">Nomor</th>
            <th style="font-weight: bold;">Produk</th>
            <th style="font-weight: bold;">Keterangan</th>
            <th style="font-weight: bold;">Kuantitas</th>
            <th style="font-weight: bold;">Satuan</th>
            <th style="font-weight: bold;">Harga Satuan</th>
            <th style="font-weight: bold;">Jumlah Tagihan</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $harga = 0;
            $total = 0;
        @endphp
        @foreach($data as $row)
        @php 
            $harga += $row->harga_satuan;
            $total += $row->total;
        @endphp 
        <tr>
            <td>{{ $row->nama_customer }}</td>
            <td>{{ $row->tanggal }}</td>
            <td>Sales Invoice</td>
            <td>{{ $row->invoice_number }}</td>
            <td>{{ $row->nama_produk }}</td>
            <td>{{ $row->keterangan }}</td>
            <td>{{ $row->qty }}</td>
            <td>{{ $row->satuan }}</td>
            <td>{{ $row->harga_satuan }}</td>
            <td>{{ $row->total }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="8" style="font-weight: bold;text-align:center">TOTAL</td>
            <td style="font-weight: bold;">{{ $harga }}</td>
            <td style="font-weight: bold;">{{ $total }}</td>
        </tr>
    </tfoot>
</table>