<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Model
{
    use HasFactory;

    function getTotalProduct($filter_date)
    {
        $filter_date = explode('-', $filter_date);
        $month = $filter_date[0];
        $year = $filter_date[1];

        $user = Auth::user();

        $data = DB::table('product')
                    ->where([['status', 1], ['flag', 1], ['uid_company', $user->uid_company]])
                    ->whereRaw('MONTH(insert_at) = ? AND YEAR(insert_at) = ?', [$month, $year])
                    ->count();      

        return $data;
    }

    function getTotalPurchase($filter_date)
    {
        $filter_date = explode('-', $filter_date);
        $month = $filter_date[0];
        $year = $filter_date[1];

        $user = Auth::user();

        $data = DB::table('purchase_orders')
                        ->where([['status', 1], ['uid_company', $user->uid_company]])
                        ->whereRaw('MONTH(transaction_date) = ? AND YEAR(transaction_date) = ?', [$month, $year])
                        ->sum('grand_total');

        return $data;
    }

    function getTotalSales($filter_date)
    {
        $filter_date = explode('-', $filter_date);
        $month = $filter_date[0];
        $year = $filter_date[1];

        $user = Auth::user();

        $data = DB::table('sales_orders')
                    ->where([['status', 1], ['uid_company', $user->uid_company]])
                    ->whereRaw('MONTH(transaction_date) = ? AND YEAR(transaction_date) = ?', [$month, $year])
                    ->sum('grand_total');

        return $data;
    }

    function getPurchaseStatistics($filter_date)
    {
        $filter_date = explode('-', $filter_date);
        $month = $filter_date[0];
        $year = $filter_date[1];

        $user = Auth::user();

        $query = DB::table('purchase_orders')
                    ->where([['status', 1], ['uid_company', $user->uid_company]])
                    ->whereRaw('MONTH(transaction_date) = ? AND YEAR(transaction_date) = ?', [$month, $year])
                    ->groupBy(DB::raw('date(transaction_date)'))
                    ->selectRaw("DATE_FORMAT(transaction_date, '%d/%m/%Y') as label, sum(grand_total) as total")
                    ->get();

        $data_chunk = $query->chunk(10);

        $arr_label = [];
        $arr_total = [];

        foreach($data_chunk as $chunk) {
            foreach($chunk as $val_chunk) {
                $arr_label[] = $val_chunk->label;
                $arr_total[] = $val_chunk->total;
            }
        }

        $data = [
            'label' => $arr_label,
            'total' => $arr_total,
        ];
                
        return $data;
    }

    function getSalesStatistics($filter_date)
    {
        $filter_date = explode('-', $filter_date);
        $month = $filter_date[0];
        $year = $filter_date[1];

        $user = Auth::user();

        $query = DB::table('sales_orders')
                    ->where([['status', 1], ['uid_company', $user->uid_company]])
                    ->whereRaw('MONTH(transaction_date) = ? AND YEAR(transaction_date) = ?', [$month, $year])
                    ->groupBy(DB::raw('date(transaction_date)'))
                    ->selectRaw("DATE_FORMAT(transaction_date, '%d/%m/%Y') as label, 
	                    sum(grand_total) as total")
                    ->get();

        $data_chunk = $query->chunk(10);

        $arr_label = [];
        $arr_total = [];

        foreach($data_chunk as $chunk) {
            foreach($chunk as $val_chunk) {
                $arr_label[] = $val_chunk->label;
                $arr_total[] = $val_chunk->total;
            }
        }

        $data = [
            'label' => $arr_label,
            'total' => $arr_total,
        ];
                
        return $data;
    }
}
