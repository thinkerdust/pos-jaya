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
        @font-face {
            font-family: 'Courier';
            font-style: normal;
            font-weight: normal;
            src: url(https://fonts.google.com/share?selection.family=Courier+Prime:ital,wght@0,400;0,700;1,400;1,700|Quicksand:wght@300..700) format('truetype');
        }

        * {
            font-family: Courier, Geneva, Tahoma, sans-serif;
        }

        table {
            font-size: small;
            font-weight: bold;
        }

        tfoot tr td {
            font-weight: bold;
            font-size: small;
        }

        .gray {
            background-color: lightgray
        }

        #footer {
            position: fixed;
            bottom: -80px;
            left: 0px;
            right: 0px;
            width: 100%;
        }

        #table {
            border-collapse: collapse;
            width: 100%;
        }

        #table td,
        #table th {
            border: 1px solid #333;
            padding: 3px;
        }

        #table th {
            padding-top: 3px;
            padding-bottom: 3px;
            text-align: left;
        }

        #tfoot {
            padding-top: 3px;
            padding-bottom: 3px;

        }

        #tfoot td,
        #tfoot th {
            padding: 2px;
        }

        @page {
            margin: 20px 20px 100px 20px;
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
            @php $no = 1;
                $grand_total = 0;
            @endphp
            @foreach ($data['detail'] as $d)
                        <?php 
                                                                                                                                                                                                                                                                                                          if ($d->length != 0 || $d->width != 0) {
                    $size = $d->length . 'x' . $d->width;

                    $subtotal = ($d->length * $d->width * $d->qty * $d->price / 10000);

                } else {
                    $size = "";

                    $subtotal = ($d->qty * $d->price);

                }
                $grand_total += $subtotal;
                                                                                                                                                                                                                                                                                                                                                                                                                        ?>

                        <tr>
                            <td scope="row">{{ $no++ }}</td>
                            <td>{{$d->product_name}}</td>
                            <td align="right">{{integers($d->price)}}</td>
                            <td align="center">{{$size}}</td>
                            <td align="right">{{integers($d->qty)}}</td>
                            <td align="right"></td>
                            <td align="right">{{integers($subtotal)}}</td>
                        </tr>
                        @if ($d->note != "")
                            <tr>
                                <td colspan="7">{{ "Ket : " . $d->note }}</td>
                            </tr>
                        @endif
            @endforeach
        </tbody>
    </table>
    <table style="width:100%" id="tfoot">
        @if ($data['header']->discount != 0)
            <tr>
                <td style="width:60%"></td>
                <td align="right">Discount</td>
                <td align="right">{{ integers($data['header']->discount)}}</td>
            </tr>

        @endif
        @if ($data['header']->tax_rate != 0)
            <tr>
                <td style="width:60%"></td>
                <td align="right">Ppn {{$data['header']->tax_rate != 0 ? "(" . $data['header']->tax_rate . "%)" : ""}}
                </td>
                <td align="right">{{integers($data['header']->tax_value)}}</td>
            </tr>
        @endif
        @if ($data['header']->proofing != 0)
            <tr>
                <td style="width:60%"></td>
                <td align="right">Proofing</td>
                <td align="right">{{ integers($data['header']->proofing)}}</td>
            </tr>
        @endif

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
                    <p style="font-size:13px;font-style:italic;font-weight:bold">Rek
                        {{$data['company']->account_number}} <br>{{'a/n ' . $data['company']->account_name}}
                    </p>
                </td>
                <td style="width:60%;border:1px solid #555;text-align:center" rowspan="2">
                    <p>{{($data['header']->paid_off == 1) ? 'LUNAS' : 'BELUM LUNAS'}}</p>
                </td>
                <td style="text-align:center">Petugas</td>
            </tr>
            <tr>
                <td></td>
                <td style="text-align:center">{{Auth::user()->username}}</td>
            </tr>
        </table>
    </div>
</body>

</html>