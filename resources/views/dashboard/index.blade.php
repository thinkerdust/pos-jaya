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

                        <div class="nk-block-head-content">
                            <div class="toggle-wrap nk-block-tools-toggle">
                                <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                                <div class="toggle-expand-content" data-content="pageMenu">
                                    <ul class="nk-block-tools g-3">
                                        
                                        <li>
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="filter_date" name="filter_date" value="{{ date('F-Y') }}" readonly>
                                                </div>
                                            </div>
                                        </li>
                                        
                                    </ul>
                                </div>
                            </div>
                        </div><!-- .nk-block-head-content -->
                        
                    </div><!-- .nk-block-between -->
                </div><!-- .nk-block-head -->

                <div class="nk-block">
                    <div class="row g-gs">
                        <div class="col-xxl-4 col-sm-4">
                            <div class="card text-white bg-primary">
                                <div class="nk-ecwg nk-ecwg6">
                                    <div class="card-inner">
                                        <div class="card-title-group">
                                            <div class="card-title">
                                                <h4 class="title"><em class="icon fas fa-box-open fa-1x"></em> Total Products</h4>
                                            </div>
                                        </div>
                                        <div class="data">
                                            <div class="fw-medium dashboard-amount text-end" id="total-products">0</div>
                                        </div>
                                    </div><!-- .card-inner -->
                                </div><!-- .nk-ecwg -->
                            </div><!-- .card -->
                        </div><!-- .col -->
                        <div class="col-xxl-4 col-sm-4">
                            <div class="card text-white bg-success">
                                <div class="nk-ecwg nk-ecwg6">
                                    <div class="card-inner">
                                        <div class="card-title-group">
                                            <div class="card-title">
                                                <h4 class="title"><em class="icon fas fa-shopping-cart fa-1x"></em> Total Purchase</h4>
                                            </div>
                                        </div>
                                        <div class="data">
                                            <div class="fw-medium dashboard-amount text-end" id="total-purchase">0</div>
                                        </div>
                                    </div><!-- .card-inner -->
                                </div><!-- .nk-ecwg -->
                            </div><!-- .card -->
                        </div><!-- .col -->
                        <div class="col-xxl-4 col-sm-4">
                            <div class="card text-white bg-info">
                                <div class="nk-ecwg nk-ecwg6">
                                    <div class="card-inner">
                                        <div class="card-title-group">
                                            <div class="card-title">
                                                <h4 class="title"><em class="icon fas fa-hand-holding-usd fa-1x"></em> Total Sales</h4>
                                            </div>
                                        </div>
                                        <div class="data">
                                            <div class="fw-medium dashboard-amount text-end" id="total-sales">0</div>
                                        </div>
                                    </div><!-- .card-inner -->
                                </div><!-- .nk-ecwg -->
                            </div><!-- .card -->
                        </div><!-- .col -->
                        <div class="col-xxl-6">
                            <div class="card card-bordered card-preview">
                                <div class="card-inner">
                                    <div class="card-head">
                                        <h6 class="title">Purchase Statistics</h6>
                                    </div>
                                    
                                    <div class="nk-ck-sm">
                                        <canvas class="purchase-statistics" id="purchaseStatistics"></canvas>
                                    </div>
                                </div>
                            </div><!-- .card-preview -->
                        </div><!-- .col -->
                        <div class="col-xxl-6">
                            <div class="card card-bordered card-preview">
                                <div class="card-inner">
                                    <div class="card-head">
                                        <h6 class="title">Sales Statistics</h6>
                                    </div>
                                    
                                    <div class="nk-ck-sm">
                                        <canvas class="sales-statistics" id="salesStatistics"></canvas>
                                    </div>
                                </div>
                            </div><!-- .card-preview -->
                        </div><!-- .col -->
                    </div><!-- .row -->
                </div><!-- .nk-block -->
                
            </div>
        </div>
    </div>
</div>

<!-- style -->
<style>
    .dashboard-amount {
        font-size: 30px;
    }
</style>
@endsection