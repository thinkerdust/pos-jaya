<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\ReportCustomerSalesExport;
use App\Exports\ReportProductSalesExport;
use App\Exports\ReportProductStockExport;
use App\Exports\ReportReceivableCustomerExport;
use App\Exports\ReportSalesExport;

use Illuminate\Support\Facades\Auth;
use App\Models\Company;

class ReportTransactionExport implements WithMultipleSheets
{
    use Exportable;

    protected $min;
    protected $max;
    
    public function __construct($min, $max)
    {
        $this->min = $min;
        $this->max = $max;
    }
    
    public function sheets(): array 
    {   
        $user = Auth::user();
        $company = Company::where('uid', $user->uid_company)->first();
        $company_name = $company->name;
        return [
            new ReportCustomerSalesExport($company_name, $this->min, $this->max),
            new ReportProductSalesExport($company_name, $this->min, $this->max),
            new ReportProductStockExport($company_name, $this->min, $this->max),
            new ReportReceivableCustomerExport($company_name, $this->min, $this->max),
            new ReportSalesExport($company_name, $this->min, $this->max)
        ];
    }
}
