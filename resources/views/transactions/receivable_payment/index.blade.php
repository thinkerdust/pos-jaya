@extends('master')

@section('content')

<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-md mx-auto">
                    <div class="nk-block-head nk-block-head-lg wide-sm">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">{{ $title }}</h3>
                        </div>
                    </div><!-- .nk-block-head -->
                    <div class="nk-block nk-block-lg">
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                @can("crudAccess", "TX1")
                                    <!-- <a href="/transaction/receivable_payment/add" class="btn btn-theme-sml btn-sm"><em
                                                class="icon ni ni-plus"></em><span>Add Data</span></a> -->
                                    <a href="/transaction/receivable_payment/export_excel"
                                        class="btn btn-success btn-sm"><em class="icon ni ni-download"></em><span>Export
                                            Excel</span></a>

                                @endcan
                                <hr class="preview-hr">
                                <table class="table table-striped nowrap" id="dt-table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>No Faktur</th>
                                            <th>Customer</th>
                                            <th>Tanggal</th>
                                            <th>Jumlah</th>
                                            <th>Termin</th>
                                            <th>Metode Pembayaran</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                </table>
                            </div>
                        </div><!-- .card-preview -->
                    </div> <!-- nk-block -->
                </div><!-- .components-preview -->
            </div>
        </div>
    </div>
</div>

@endsection