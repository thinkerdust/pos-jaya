<table>
    <thead>
        <tr>
            <th colspan="8" style="font-weight: bold; text-align: center;font-size:16px;">{{ $company_name }}</th>
        </tr>
        <tr>
            <th colspan="8" style="font-weight: bold; text-align: center;font-size:16px;">Daftar Faktur Penjualan</th>
        </tr>
        <tr>
            <th colspan="8" style="font-weight: bold; text-align: center;font-size:16px;">{{ $min }} - {{ $max }}</th>
        </tr>
        <tr>
            <th colspan="8" style="font-weight: bold; text-align: center;font-size:16px;">dalam IDR</th>
        </tr>
        <tr>
            <th style="font-weight: bold;">Tanggal</th>
            <th style="font-weight: bold;">Tipe Transaksi</th>
            <th style="font-weight: bold;">Nomor Transaksi</th>
            <th style="font-weight: bold;">Nama Pelanggan</th>
            <th style="font-weight: bold;">Status Bayar</th>
            <th style="font-weight: bold;">Memo</th>
            <th style="font-weight: bold;">Total</th>
            <th style="font-weight: bold;">Sisa Tagihan</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $total = 0;
            $sisa = 0;
        @endphp
        @foreach($data as $row)
        @php 
            $total += $row->grand_total;
            $sisa += $row->sisa_tagihan;
        @endphp 
        <tr>
            <td>{{ $row->tanggal_transaksi }}</td>
            <td>Faktur Penjualan</td>
            <td>{{ $row->invoice_number }}</td>
            <td>{{ $row->nama_customer }}</td>
            <td>{{ $row->status_bayar }}</td>
            <td>{{ $row->memo }}</td>
            <td>{{ $row->grand_total }}</td>
            <td>{{ $row->sisa_tagihan }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6" style="font-weight: bold;text-align:center">TOTAL</td>
            <td style="font-weight: bold;">{{ $total }}</td>
            <td style="font-weight: bold;">{{ $sisa }}</td>
        </tr>
    </tfoot>
</table>
