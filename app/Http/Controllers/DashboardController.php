<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Models\Dashboard;
use DataTables;

class DashboardController extends Controller
{
    public function index() 
    {
        // $js = 'js/apps/dashboard/index.js?_='.rand();
        return view('dashboard.index');
    }
    
}
