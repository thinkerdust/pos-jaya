<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dashboard;
use Yajra\DataTables\DataTables;

class DashboardController extends Controller
{
    function __construct()
    {
        $this->dashboard = new Dashboard();
    }

    public function index() 
    {
        $js = 'js/apps/dashboard/index.js?_='.rand();
        return view('dashboard.index', compact('js'));
    }

    public function total_products(Request $request)
    {
        $filter_date = $request->filter_date;
        $filter_date = date('m-Y', strtotime($filter_date));
        $data = $this->dashboard->getTotalProduct($filter_date);
        return response()->json($data);
    }

    public function total_purchase(Request $request)
    {
        $filter_date = $request->filter_date;
        $filter_date = date('m-Y', strtotime($filter_date));
        $data = $this->dashboard->getTotalPurchase($filter_date);
        return response()->json($data);
    }

    public function total_sales(Request $request)
    {
        $filter_date = $request->filter_date;
        $filter_date = date('m-Y', strtotime($filter_date));
        $data = $this->dashboard->getTotalSales($filter_date);
        return response()->json($data);
    }

    public function purchase_statistics(Request $request)
    {
        $filter_date = $request->filter_date;
        $filter_date = date('m-Y', strtotime($filter_date));
        $data = $this->dashboard->getPurchaseStatistics($filter_date);
        return response()->json($data);
    }

    public function sales_statistics(Request $request)
    {
        $filter_date = $request->filter_date;
        $filter_date = date('m-Y', strtotime($filter_date));
        $data = $this->dashboard->getSalesStatistics($filter_date);
        return response()->json($data);
    }
    
}
