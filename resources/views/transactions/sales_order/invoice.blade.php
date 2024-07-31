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
            border: 1px solid #333;
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

        @page {
            margin: 20px;
        }
    </style>


</head>

<body>

    <table width="100%">
        <tr>
            <td valign="top"><img src="{{ 'storage/' . $data['company']->photo}}" alt="" width="80" /></td>
            <td style="vertical-align:top">
                <h3 style=" margin:0px;padding:0px">{{$data['company']->name}}</h3>
                <p style="margin:0px;padding:0px">{{$data['company']->address}} <br>
                    {{$data['company']->phone}}</p>
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
            <td>{{ \Carbon\Carbon::parse(now())->format('d/m/Y')}}</td>
        </tr>
        <tr>
            <td><strong>Customer</strong></td>
            <td>:</td>
            <td>{{ $data['header']->name}}</td>
            <td><strong>Jam</strong></td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse(now())->format('H.i')}}</td>
        </tr>
        <tr>
            <td><strong>Telp</strong></td>
            <td>:</td>
            <td>{{ $data['header']->phone}}</td>
            <td><strong></strong></td>
            <td></td>
            <td></td>
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
            <td align="right" style="font-weight:bold">{{"Rp." . integers($data['header']->grand_total)}}</td>
        </tr>
        @if ($data['receipt'] !== 0)
            <tr>
                <td style="width:60%;"></td>
                <td align="right" style="font-weight:bold">Bayar</td>
                <td align="right" style="font-weight:bold">{{"Rp." . integers($data['receipt'])}}</td>
            </tr>
            <tr>
                <td style="width:60%;"></td>
                <td align="right" style="font-weight:bold">Tagihan</td>
                <td align="right" style="font-weight:bold">
                    {{"Rp. " . integers($data['header']->grand_total - $data['receipt'])}}
                </td>
            </tr>


        @endif


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