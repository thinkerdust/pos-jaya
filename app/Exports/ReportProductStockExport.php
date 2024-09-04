<?php

namespace App\Exports;

use App\Models\SalesOrder;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportProductStockExport implements ShouldAutoSize, WithTitle, FromView
{
    protected $company_name;
    protected $min;
    protected $max;

    function __construct($company_name, $min, $max)
    {
        $this->company_name = $company_name;
        $this->min = $min;
        $this->max = $max;
    }

    public function view(): View
    {
        $sales = new SalesOrder();
        return view('transactions.exports.product_stock', [
            'data' => $sales->exportReportProductStock($this->min, $this->max),
            'min' => date('d/m/Y', strtotime($this->min)),
            'max' => date('d/m/Y', strtotime($this->max)),
            'company_name' => $this->company_name
        ]);
    }

    public function title(): string
    {
        return 'RINGKASAN PERSEDIAAN BARANG';
    }
}
