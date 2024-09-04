<table>
    <thead>
        <tr>
            <th colspan="11" style="font-weight: bold; text-align: center;font-size:16px;">{{ $company_name }}</th>
        </tr>
        <tr>
            <th colspan="11" style="font-weight: bold; text-align: center;font-size:16px;">Penjualan dengan Produk</th>
        </tr>
        <tr>
            <th colspan="11" style="font-weight: bold; text-align: center;font-size:16px;">{{ $min }} - {{ $max }}</th>
        </tr>
        <tr>
            <th colspan="11" style="font-weight: bold; text-align: center;font-size:16px;">dalam IDR</th>
        </tr>
        <tr>
            <th style="font-weight: bold;">Produk</th>
            <th style="font-weight: bold;">Tanggal</th>
            <th style="font-weight: bold;">Transaksi</th>
            <th style="font-weight: bold;">Nomor</th>
            <th style="font-weight: bold;">Customer</th>
            <th style="font-weight: bold;">Kuantitas</th>
            <th style="font-weight: bold;">Satuan</th>
            <th style="font-weight: bold;">Harga Satuan</th>
            <th style="font-weight: bold;">Harga</th>
            <th style="font-weight: bold;">Diskon</th>
            <th style="font-weight: bold;">Total</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $subtotal = 0;
            $total = 0;
        @endphp
        @foreach($data as $row)
        @php 
            $subtotal += $row->harga;
            $total += $row->total;
        @endphp 
        <tr>
            <td>{{ $row->nama_produk }}</td>
            <td>{{ $row->tanggal }}</td>
            <td>Sales Invoice</td>
            <td>{{ $row->invoice_number }}</td>
            <td>{{ $row->nama_customer }}</td>
            <td>{{ $row->qty }}</td>
            <td>{{ $row->satuan }}</td>
            <td>{{ $row->harga_satuan }}</td>
            <td>{{ $row->harga }}</td>
            <td>{{ $row->diskon }}</td>
            <td>{{ $row->total }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="8" style="font-weight: bold;text-align:center">TOTAL</td>
            <td style="font-weight: bold;">{{ $subtotal }}</td>
            <td></td>
            <td style="font-weight: bold;">{{ $total }}</td>
        </tr>
    </tfoot>
</table>