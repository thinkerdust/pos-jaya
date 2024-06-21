<?php 
function integers($angka)
{
    $detect_dot = strpos($angka, '.');
    if ($detect_dot > 0) {
        $explode = explode('.', $angka);
        $number = number_format($explode[0], 0, ",", ".");

        $after_comma = substr($explode[1], 0, 2);
        if (strlen($explode[1]) == 1) {
            $after_comma = $explode[1] . '0';
        }

        $final_number = $number;
    } else {
        $number = number_format($angka, 0, ",", ".");
        $final_number = $number;
    }
    return $final_number;
}

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>INVOICE {{ $data['header']->invoice_number }}</title>

    <style type="text/css">
        * {
            font-family: Verdana, Arial, sans-serif;
        }

        table {
            font-size: x-small;
        }

        tfoot tr td {
            font-weight: bold;
            font-size: x-small;
        }

        .gray {
            background-color: lightgray
        }

        #footer {
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        #table {
            border-collapse: collapse;
            width: 100%;
        }

        #table td,
        #table th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        /* #table tr:nth-child(even) {
            background-color: #f2f2f2;
        } */

        #table th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
        }

        #tfoot {
            padding-top: 5px;
            padding-bottom: 10px;

        }

        #tfoot td,
        #tfoot th {
            padding: 3px;
        }
    </style>


</head>

<body>

    <table width="100%">
        <tr>
            <td valign="top"><img
                    src="data:image/png;base64,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"
                    alt="" width="80" /></td>
            <td style="vertical-align:top">
                <h3 style=" margin:0px;padding:0px">MARTIN MULTIGRAFIKA</h3>
                <p style="margin:0px;padding:0px">Jl. Hasanudin No.32, Purwosari, Kec. Laweyan, Kota
                    Surakarta, Jawa Tengah 57142 <br>
                    0271-718237<br>
                    089 674845990</p>
            </td>
        </tr>
    </table>

    <table width="100%" style="margin-top:10px">
        <tr>
            <td style="width:12%"><strong>Nota</strong></td>
            <td style="width:5%">:</td>
            <td style="width:50%">{{ $data['header']->invoice_number}}</td>
            <td style="width:12%"><strong>Tanggal</strong></td>
            <td style="width:5%">:</td>
            <td>{{ \Carbon\Carbon::parse($data['header']->transaction_date)->format('d/m/Y')}}</td>
        </tr>
        <tr>
            <td><strong>Customer</strong></td>
            <td>:</td>
            <td>{{ $data['header']->name}}</td>
            <td><strong>Jam</strong></td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($data['header']->transaction_date)->format('H.i')}}</td>
        </tr>
        <tr>
            <td><strong>Telp</strong></td>
            <td>:</td>
            <td>{{ $data['header']->phone}}</td>
            <td><strong>Tgl. Tempo</strong></td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($data['header']->collection_date)->format('d/m/Y')}}</td>
        </tr>



    </table>

    <br />

    <table width="100%" id="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>Size</th>
                <th>Qty</th>
                <th>Tambahan</th>
                <th>Sub Total</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($data['detail'] as $d)
                @php 
                    $subtotal = $d->qty * $d->price;
                @endphp

                <tr>
                    <td scope="row">{{ $no++ }}</td>
                    <td>{{$d->product_name}}</td>
                    <td align="right">{{integers($d->price)}}</td>
                    <td align="center">-</td>
                    <td align="right">{{integers($d->qty)}}</td>
                    <td align="left"></td>
                    <td align="right">{{integers($subtotal)}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <table style="width:100%" id="tfoot">
        <tr>
            <td style="width:60%"></td>
            <td align="right">Subtotal</td>
            <td align="right">{{ integers($subtotal)}}</td>
        </tr>
        <tr>
            <td style="width:60%"></td>
            <td align="right">Discount</td>
            <td align="right">{{ integers($data['header']->discount)}}</td>
        </tr>
        <tr>
            <td style="width:60%"></td>
            <td align="right">Ppn {{$data['header']->tax_rate != 0 ? "(" . $data['header']->tax_rate . "%)" : ""}}
            </td>
            <td align="right">{{integers($data['header']->tax_value)}}</td>
        </tr>

        <tr>
            <td style="width:60%;"></td>
            <td align="right" style="font-weight:bold">Total</td>
            <td align="right" style="font-weight:bold">{{"Rp. " . integers($data['header']->grand_total)}}</td>
        </tr>
    </table>
    <div id="footer">
        <hr>
        <table style="width:100%">
            <tr>
                <td>
                    <p style="font-size:11px;font-style:italic;color:#555">Rek BCA 0152830031 <br> A/n Martinus Budi
                    </p>
                </td>
                <td style="width:60%"></td>
                <td style="text-align:center">Petugas</td>
            </tr>
            <tr>
                <td></td>
                <td style="width:60%"></td>
                <td style="text-align:center">{{Auth::user()->username}}</td>
            </tr>
        </table>
    </div>
</body>

</html>