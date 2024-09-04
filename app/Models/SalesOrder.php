<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class SalesOrder extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'sales_orders';

    public function dataTableSalesOrders($min, $max, $status, $role)
    {
        $user = Auth::user();
        $query = DB::table('sales_orders as so')->join('customer as cus', 'so.uid_customer', '=', 'cus.uid')->leftJoin('sales_order_details as sod', function ($join) {
            $join->on('sod.invoice_number', '=', 'so.invoice_number');
            $join->on('sod.uid_company', '=', 'so.uid_company');
        })->select('so.uid', 'so.invoice_number', 'cus.name', DB::raw('DATE_FORMAT(so.transaction_date, "%d/%m/%Y") as transaction_date'), DB::raw('GROUP_CONCAT( sod.note) as note'), 'so.grand_total', 'so.paid_off', 'so.uid_company')->where('so.status', 1)->where('so.pending', 0)->groupBy('so.invoice_number','so.uid_company');

        if (!empty($min) && !empty($max)) {
            $query->whereBetween('so.transaction_date', [$min, $max]);
        }

        if ($status !== null) {
            $query->where('so.paid_off', $status);
        }

        if ($role == 3) {
            $query->where('so.insert_by', $user->id);
        } else {
            $query->where('so.uid_company', $user->uid_company);
        }

        $order = request('order')[0];
        if ($order['column'] == '0') {
            $query->orderBy(DB::raw("SUBSTRING(so.invoice_number, 3, 8)"), 'DESC')
            ->orderBy(DB::raw("CAST(SUBSTRING_INDEX(so.invoice_number, '-', -1) AS UNSIGNED)"), 'DESC');
        }

        return $query;
    }

    public function dataTablePending($min, $max, $role)
    {
        $user = Auth::user();
        $query = DB::table('sales_orders as so')->join('customer as cus', 'so.uid_customer', '=', 'cus.uid')->select('so.uid', 'so.invoice_number', 'cus.name', DB::raw('DATE_FORMAT(so.transaction_date, "%d/%m/%Y") as transaction_date'), DB::raw('(SELECT GROUP_CONCAT( sod.note) from sales_order_details sod where sod.invoice_number=so.invoice_number and sod.status=1) as note'), 'so.grand_total', 'so.paid_off')->where('so.status', 1)->where('so.pending', 1);

        if (!empty($min) && !empty($max)) {
            $query->whereBetween('so.transaction_date', [$min, $max]);
        }

        if ($role == 3) {
            $query->where('so.insert_by', $user->id);
        } else {
            $query->where('so.uid_company', $user->uid_company);
        }


        $order = request('order')[0];
        if ($order['column'] == '0') {
            $query->orderBy(DB::raw("SUBSTRING(so.invoice_number, 3, 8)"), 'DESC')
            ->orderBy(DB::raw("CAST(SUBSTRING_INDEX(so.invoice_number, '-', -1) AS UNSIGNED)"), 'DESC');
        }

        return $query;
    }


    public function listDataSalesOrders($q)
    {
        $data = DB::table('sales_orders as so')->join('customer as cus', 'so.uid_customer', '=', 'cus.uid')->select('so.uid', 'so.invoice_number', 'cus.name', 'so.transaction_date', 'so.note', 'so.grand_total')->where('so.status', 1)->where('so.pending', 0);
        if ($q) {
            $data = $data->where('cus.name', 'like', '%' . $q . '%');
        }
        return $data->get();
    }

    public function listDataPending($q)
    {
        $data = DB::table('sales_orders as so')->join('customer as cus', 'so.uid_customer', '=', 'cus.uid')->select('so.uid', 'so.invoice_number', 'cus.name', 'so.transaction_date', 'so.note', 'so.grand_total')->where('so.status', 1)->where('so.pending', 1);
        if ($q) {
            $data = $data->where('cus.name', 'like', '%' . $q . '%');
        }
        return $data->get();
    }

    public function exportReportSales($min, $max) 
    {
        $user = Auth::user();
        $query = DB::select("SELECT DATE_FORMAT(so.transaction_date, '%d/%m/%Y') as tanggal_transaksi, 
                        so.invoice_number,
                        upper(c.name) as nama_customer,
                        IF(so.paid_off = 1, 'LUNAS', 'BELUM LUNAS') as status_bayar,
                        GROUP_CONCAT(sod.note SEPARATOR ', ') as memo,
                        so.grand_total,
                        coalesce(rp.bayar, 0) as bayar,
                        (so.grand_total - coalesce(rp.bayar, 0)) as sisa_tagihan
                    FROM sales_orders so
                    join sales_order_details sod on so.invoice_number = sod.invoice_number
                        and so.uid_company = sod.uid_company
                    join customer c on so.uid_customer = c.uid
                    left join (
                        SELECT rp.uid_company, rp.invoice_number, sum(rp.pay-rp.changes) as bayar
                        from receivable_payments rp 
                        where rp.status = 1
                            and rp.transaction_date between '$min' and '$max'
                            and rp.uid_company = '$user->uid_company'
                        group by rp.invoice_number, rp.uid_company 
                    ) rp on so.invoice_number = rp.invoice_number
                        and so.uid_company = rp.uid_company
                    where so.transaction_date between '$min' and '$max'
                        and so.uid_company = '$user->uid_company'
                        and so.status = 1 and so.pending = 0
                        and sod.status = 1
                    group by so.invoice_number
                    order by so.transaction_date");
                
        return $query;
    }

    public function exportReportProductSales($min, $max)
    {
        $user = Auth::user();
        $query = DB::select("SELECT p.name as nama_produk, 
                        DATE_FORMAT(so.transaction_date, '%d/%m/%Y') as tanggal, so.invoice_number, c.name as nama_customer,
                        sod.qty, u.name as satuan, sod.price as harga_satuan, sod.discount as diskon,
                        (case when sod.`length` != 0 and sod.width != 0
                            then ((sod.qty * sod.price * sod.`length` * sod.width) / 10000)
                            else (sod.qty * sod.price)
                        end) as harga,
                        (case when sod.`length` != 0 and sod.width != 0
                            then (((sod.qty * sod.price * sod.`length` * sod.width) / 10000) - (((sod.qty * sod.price * sod.`length` * sod.width) / 10000) * sod.discount / 100))
                            else ((sod.qty * sod.price) - ((sod.qty * sod.price) * sod.discount / 100)) 
                        end) as total
                    FROM sales_orders so 
                    JOIN sales_order_details sod 
                        on so.invoice_number = sod.invoice_number 
                        and so.uid_company = sod.uid_company 
                    JOIN product p 
                        on p.uid = sod.uid_product 
                        and p.uid_company = sod.uid_company 
                    JOIN customer c on c.uid = so.uid_customer 
                    JOIN unit u on u.uid = p.uid_unit 
                    where so.status = 1 and so.pending = 0 and sod.status = 1 and p.status = 1 and c.status = 1 and u.status = 1
                        and so.transaction_date between '$min' and '$max'
                        and so.uid_company = '$user->uid_company'
                    order by p.name, so.transaction_date");
        
        return $query;
    }

    public function exportReportCustomerSales($min, $max) 
    {
        $user = Auth::user();
        $query = DB::select("SELECT c.name as nama_customer, 
                        DATE_FORMAT(so.transaction_date, '%d/%m/%Y') as tanggal,
                        so.invoice_number,
                        p.name as nama_produk,
                        sod.note as keterangan, sod.qty,
                        u.name as satuan,
                        sod.price as harga_satuan,
                        (case when sod.`length` != 0 and sod.width != 0
                            then (((sod.qty * sod.price * sod.`length` * sod.width) / 10000) - (((sod.qty * sod.price * sod.`length` * sod.width) / 10000) * sod.discount / 100))
                            else ((sod.qty * sod.price) - ((sod.qty * sod.price) * sod.discount / 100)) 
                        end) as total
                    FROM sales_orders so 
                    JOIN sales_order_details sod 
                        on so.invoice_number = sod.invoice_number 
                        and so.uid_company = sod.uid_company 
                    JOIN product p 
                        on p.uid = sod.uid_product 
                        and p.uid_company = sod.uid_company 
                    JOIN customer c on c.uid = so.uid_customer 
                    JOIN unit u on u.uid = p.uid_unit 
                    WHERE so.status = 1 and so.pending = 0 and sod.status = 1 and p.status = 1 and c.status = 1 and u.status = 1
                        and so.transaction_date between '$min' and '$max'
                        and so.uid_company = '$user->uid_company'
                    ORDER BY c.name, so.transaction_date");

        return $query;
    }

    public function exportReportProductStock($min, $max)
    {
        $user = Auth::user();
        $query = DB::select("SELECT p.kode, p.name as nama_produk, p.stock,
                        COALESCE(pod.rata_harga, 0) as rata_harga,
                        (p.stock * COALESCE(pod.rata_harga, 0)) as total_harga,
                        u.name as satuan
                    FROM product p 
                    join unit u on p.uid_unit = u.uid 
                    left join (
                        select pod.uid_product, ROUND(AVG(pod.price), 2) as rata_harga 
                        from purchase_order_details pod 
                        join purchase_orders po on pod.po_number = po.po_number
                            and pod.uid_company = po.uid_company
                        where pod.status = 1 and pod.uid_company = '$user->uid_company'
                            and po.status = 1
                            and po.transaction_date between '$min' and '$max' 
                        group by pod.uid_product
                    ) pod on p.uid = pod.uid_product
                    where p.status = 1 and p.uid_company = '$user->uid_company'
                        and u.status = 1
                    order by p.name");

        return $query;
    }

    public function exportReportReceivableCustomer($min, $max)
    {
        $user = Auth::user();
        $query = DB::select("SELECT c.name as nama_pelanggan, 
                        DATE_FORMAT(so.transaction_date, '%d/%m/%Y') as tanggal,
                        DATE_FORMAT(so.collection_date, '%d/%m/%Y') as jatuh_tempo,
                        so.invoice_number,
                        so.grand_total as jumlah, 
                        COALESCE(rp.bayar,0) as pemotongan,
                        (so.grand_total - COALESCE(rp.bayar,0)) as sisa_piutang
                    FROM sales_orders so 
                    join customer c on so.uid_customer = c.uid 
                    left join (
                        select rp.uid_company, rp.invoice_number, sum(rp.pay-rp.changes) as bayar 
                        from receivable_payments rp 
                        where rp.status = 1
                            and rp.transaction_date between '$min' and '$max'
                            and rp.uid_company = '$user->uid_company'
                        group by rp.invoice_number, rp.uid_company
                    ) as rp on so.invoice_number = rp.invoice_number
                        and so.uid_company = rp.uid_company
                    WHERE so.status = 1 and so.pending = 0 and so.uid_company = '$user->uid_company'
                        and so.transaction_date between '$min' and '$max' 
                    order by c.name, so.transaction_date");

        return $query;
    }

}
