@extends('master')

@section('content')

<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Welcome, {{ Auth::user()->username }}</h3>
                        </div><!-- .nk-block-head-content -->

                        @if (Auth::user()->id_level != 3)
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                                    <div class="toggle-expand-content" data-content="pageMenu">
                                        <ul class="nk-block-tools g-3">

                                            <li>
                                                <div class="form-group">
                                                    <div class="form-control-wrap">
                                                        <select class="form-control" name="mall" id="mall"></select>
                                                    </div>
                                                </div>
                                            </li>
                                            
                                            <li>
                                                <div class="form-group">
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="filter_date" name="filter_date" value="{{ date('F-Y') }}" readonly>
                                                    </div>
                                                </div>
                                            </li>

                                            <li class="nk-block-tools-opt"><a href="javascript:;" onclick="print_report()" class="btn btn-primary"><em class="icon ni ni-reports"></em><span>Download Report</span></a></li>
                                            
                                        </ul>
                                    </div>
                                </div>
                            </div><!-- .nk-block-head-content -->
                        @endif
                        
                    </div><!-- .nk-block-between -->
                </div><!-- .nk-block-head -->

                @if (Auth::user()->id_level != 3)
                <div class="nk-block">
                    <div class="row g-gs">
                        <div class="col-xxl-3 col-sm-3">
                            <div class="card text-white bg-primary">
                                <div class="nk-ecwg nk-ecwg6">
                                    <div class="card-inner">
                                        <div class="card-title-group">
                                            <div class="card-title">
                                                <h6 class="title"><em class="icon fas fa-dollar-sign fa-1x"></em> Today Orders</h6>
                                            </div>
                                        </div>
                                        <div class="data">
                                            <div class="data-group">
                                                <div class="fw-medium dashboard-amount" id="today-orders">0</div>
                                            </div>
                                        </div>
                                    </div><!-- .card-inner -->
                                </div><!-- .nk-ecwg -->
                            </div><!-- .card -->
                        </div><!-- .col -->
                        <div class="col-xxl-3 col-sm-3">
                            <div class="card text-white bg-secondary">
                                <div class="nk-ecwg nk-ecwg6">
                                    <div class="card-inner">
                                        <div class="card-title-group">
                                            <div class="card-title">
                                                <h6 class="title"><em class="icon fas fa-database fa-1x"></em> This Month Orders</h6>
                                            </div>
                                        </div>
                                        <div class="data">
                                            <div class="data-group">
                                                <div class="fw-medium dashboard-amount" id="month-orders">0</div>
                                            </div>
                                        </div>
                                    </div><!-- .card-inner -->
                                </div><!-- .nk-ecwg -->
                            </div><!-- .card -->
                        </div><!-- .col -->
                        <div class="col-xxl-3 col-sm-3">
                            <div class="card text-white bg-success">
                                <div class="nk-ecwg nk-ecwg6">
                                    <div class="card-inner">
                                        <div class="card-title-group">
                                            <div class="card-title">
                                                <h6 class="title"><em class="icon fas fa-user fa-1x"></em> Today Customers</h6>
                                            </div>
                                        </div>
                                        <div class="data">
                                            <div class="data-group">
                                                <div class="fw-medium dashboard-amount" id="today-customers">0</div>
                                            </div>
                                        </div>
                                    </div><!-- .card-inner -->
                                </div><!-- .nk-ecwg -->
                            </div><!-- .card -->
                        </div><!-- .col -->
                        <div class="col-xxl-3 col-sm-3">
                            <div class="card text-white bg-info">
                                <div class="nk-ecwg nk-ecwg6">
                                    <div class="card-inner">
                                        <div class="card-title-group">
                                            <div class="card-title">
                                                <h6 class="title fs-15px"><em class="icon fas fa-user-friends fa-1x"></em> This Month Customer</h6>
                                            </div>
                                        </div>
                                        <div class="data">
                                            <div class="data-group">
                                                <div class="fw-medium dashboard-amount" id="month-customer">0</div>
                                            </div>
                                        </div>
                                    </div><!-- .card-inner -->
                                </div><!-- .nk-ecwg -->
                            </div><!-- .card -->
                        </div><!-- .col -->
                        <div class="col-xxl-3 col-sm-3">
                            <div class="card text-white bg-indigo">
                                <div class="nk-ecwg nk-ecwg6">
                                    <div class="card-inner">
                                        <div class="card-title-group">
                                            <div class="card-title">
                                                <h6 class="title"><em class="icon fas fa-star-of-life fa-1x"></em> Total Points</h6>
                                            </div>
                                        </div>
                                        <div class="data">
                                            <div class="data-group">
                                                <div class="fw-medium dashboard-amount" id="total-points">0</div>
                                            </div>
                                        </div>
                                    </div><!-- .card-inner -->
                                </div><!-- .nk-ecwg -->
                            </div><!-- .card -->
                        </div><!-- .col -->
                        <div class="col-xxl-3 col-sm-3">
                            <div class="card text-white bg-orange">
                                <div class="nk-ecwg nk-ecwg6">
                                    <div class="card-inner">
                                        <div class="card-title-group">
                                            <div class="card-title">
                                                <h6 class="title"><em class="icon fas fa-star fa-1x"></em> Member's Point</h6>
                                            </div>
                                        </div>
                                        <div class="data">
                                            <div class="data-group">
                                                <div class="fw-medium dashboard-amount" id="member-points">0</div>
                                            </div>
                                        </div>
                                    </div><!-- .card-inner -->
                                </div><!-- .nk-ecwg -->
                            </div><!-- .card -->
                        </div><!-- .col -->
                        <div class="col-xxl-3 col-sm-3">
                            <div class="card text-white bg-purple">
                                <div class="nk-ecwg nk-ecwg6">
                                    <div class="card-inner">
                                        <div class="card-title-group">
                                            <div class="card-title">
                                                <h6 class="title"><em class="icon fas fa-ticket-alt fa-1x"></em> Member's Voucher</h6>
                                            </div>
                                        </div>
                                        <div class="data">
                                            <div class="data-group">
                                                <div class="fw-medium dashboard-amount" id="member-vouchers">0</div>
                                            </div>
                                        </div>
                                    </div><!-- .card-inner -->
                                </div><!-- .nk-ecwg -->
                            </div><!-- .card -->
                        </div><!-- .col -->
                        <div class="col-xxl-3 col-sm-3">
                            <div class="card text-white bg-teal">
                                <div class="nk-ecwg nk-ecwg6">
                                    <div class="card-inner">
                                        <div class="card-title-group">
                                            <div class="card-title">
                                                <h6 class="title"><em class="icon fas fa-times-circle fa-1x"></em> Voucher Expired</h6>
                                            </div>
                                        </div>
                                        <div class="data">
                                            <div class="data-group">
                                                <div class="fw-medium dashboard-amount" id="vouchers-expired">0</div>
                                            </div>
                                        </div>
                                    </div><!-- .card-inner -->
                                </div><!-- .nk-ecwg -->
                            </div><!-- .card -->
                        </div><!-- .col -->
                        <div class="col-xxl-6">
                            <div class="card card-bordered card-preview">
                                <div class="card-inner">
                                    <div class="card-head">
                                        <h6 class="title">Sales Statistics</h6>
                                        <div class="card-tools">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <select class="form-select" name="period_sales" id="period_sales">
                                                        <option value="day">Daily</option>
                                                        <option value="week">Weekly</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="nk-ck-sm">
                                        <canvas class="sales-statistics" id="salesStatistics"></canvas>
                                    </div>

                                    <ul class="nk-ecwg8-legends">
                                        <li>
                                            <div class="title">
                                                <span class="dot dot-lg sq" data-bg="#6576ff"></span>
                                                <span>All Sales</span>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="title">
                                                <span class="dot dot-lg sq" data-bg="#eb6459"></span>
                                                <span>Dine</span>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="title">
                                                <span class="dot dot-lg sq" data-bg="#04bf42"></span>
                                                <span>Shop</span>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div><!-- .card-preview -->
                        </div>
                        <div class="col-xxl-6">
                            <div class="card card-bordered card-preview">
                                <div class="card-inner">
                                    <div class="card-head">
                                        <h6 class="title">Customer Statistics</h6>
                                        <div class="card-tools">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <select class="form-select" name="period_customer" id="period_customer">
                                                        <option value="day">Daily</option>
                                                        <option value="week">Weekly</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="nk-ck-sm">
                                        <canvas class="customer-statistics" id="customerStatistics"></canvas>
                                    </div>

                                    <ul class="nk-ecwg8-legends">
                                        <li>
                                            <div class="title">
                                                <span class="dot dot-lg sq" data-bg="#1f64c4"></span>
                                                <span>Active User</span>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="title">
                                                <span class="dot dot-lg sq" data-bg="#142133"></span>
                                                <span>New User</span>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div><!-- .card-preview -->
                        </div>
                        <div class="col-xxl-3 col-sm-6">
                            <div class="card card-bordered card-preview">
                                <div class="card-inner">
                                    <div class="card-title-group">
                                        <div class="card-title">
                                            <h3 class="title">Top Tenants</h3>
                                        </div>
                                    </div>
                                    <br>
                                    
                                    <table class="table table-striped nowrap" id="dt-table-top-tenants">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tenant</th> 
                                                <th>Nominal</th> 
                                            </tr>
                                        </thead>
                                    </table>
                                </div><!-- .card-inner -->
                            </div><!-- .card -->
                        </div><!-- .col -->
                        <div class="col-xxl-3 col-sm-6">
                            <div class="card card-bordered card-preview">
                                <div class="card-inner">
                                    <div class="card-title-group">
                                        <div class="card-title">
                                            <h3 class="title">Top Spender</h3>
                                        </div>
                                    </div>
                                    <br>
                                    
                                    <table class="table table-striped nowrap" id="dt-table-top-spender">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Member</th> 
                                                <th>Nominal</th> 
                                            </tr>
                                        </thead>
                                    </table>
                                </div><!-- .card-inner -->
                            </div><!-- .card -->
                        </div><!-- .col -->
                        <div class="col-xxl-3 col-sm-6">
                            <div class="card card-bordered card-preview">
                                <div class="card-inner">
                                    <div class="card-title-group">
                                        <div class="card-title">
                                            <h3 class="title">Top Referral</h3>
                                        </div>
                                    </div>
                                    <br>
                                    
                                    <table class="table table-striped nowrap" id="dt-table-top-referral">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Member</th> 
                                                <th>Poin</th> 
                                            </tr>
                                        </thead>
                                    </table>
                                </div><!-- .card-inner -->
                            </div><!-- .card -->
                        </div><!-- .col -->
                    </div><!-- .row -->
                </div><!-- .nk-block -->
                @endif
                
            </div>
        </div>
    </div>
</div>

<!-- Modal -->

<style>
    @media print {
        @page {
            size: landscape;
            margin: 0;
        }
    }

    .dashboard-amount {
        font-size: 25px;
    }

    .text-right {
        text-align: right;
    }
</style>
@endsection