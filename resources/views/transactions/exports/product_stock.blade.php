<table>
    <thead>
        <tr>
            <th colspan="6" style="font-weight: bold; text-align: center;font-size:16px;">{{ $company_name }}</th>
        </tr>
        <tr>
            <th colspan="6" style="font-weight: bold; text-align: center;font-size:16px;">Ringkasan Persediaan Barang</th>
        </tr>
        <tr>
            <th colspan="6" style="font-weight: bold; text-align: center;font-size:16px;">{{ $min }} - {{ $max }}</th>
        </tr>
        <tr>
            <th colspan="6" style="font-weight: bold; text-align: center;font-size:16px;">dalam IDR</th>
        </tr>
        <tr>
            <th style="font-weight: bold;">Kode Produk</th>
            <th style="font-weight: bold;">Nama Produk</th>
            <th style="font-weight: bold;">Qty</th>
            <th style="font-weight: bold;">Satuan</th>
            <th style="font-weight: bold;">Harga Rata-Rata</th>
            <th style="font-weight: bold;">Nilai</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $subtotal = 0;
            $total = 0;
        @endphp
        @foreach($data as $row)
        @php 
            $subtotal += $row->rata_harga;
            $total += $row->total_harga;
        @endphp 
        <tr>
            <td>{{ $row->kode }}</td>
            <td>{{ $row->nama_produk }}</td>
            <td>{{ $row->stock }}</td>
            <td>{{ $row->satuan }}</td>
            <td>{{ $row->rata_harga }}</td>
            <td>{{ $row->total_harga }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" style="font-weight: bold;text-align:center">TOTAL</td>
            <td style="font-weight: bold;">{{ $subtotal }}</td>
            <td style="font-weight: bold;">{{ $total }}</td>
        </tr>
    </tfoot>
</table>