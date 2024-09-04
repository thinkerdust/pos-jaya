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
                                    <a href="/transaction/purchase/add" class="btn btn-theme-sml btn-sm"><em
                                            class="icon ni ni-plus"></em><span>Add Data</span></a>
                                    <a id="export_excel" class="btn btn-success btn-sm"><em
                                            class="icon ni ni-download"></em><span>Export
                                            Excel</span></a>
                                    <button class="btn btn-primary btn-sm" onclick="filter()"><em
                                            class="icon ni ni-filter"></em><span>Filter</span></button>
                                @endcan
                                <hr class="preview-hr">
                                <table class="table table-striped nowrap" id="dt-table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>No Faktur</th>
                                            <th>Supplier</th>
                                            <th>Tanggal</th>
                                            <th>Total</th>
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
<div class="modal fade" tabindex="-1" id="modal_filter">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                <em class="icon ni ni-cross"></em>
            </a>
            <div class="modal-header">
                <h5 class="modal-title" id="title_modal_filter">Filter Penjualan</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <label class="form-label">Datepicker Range</label>
                        <div class="form-control-wrap">
                            <div class="input-daterange date-picker-range input-group">
                                <input type="text" class="form-control" id="filter_date_from" value="{{ date('m/01/Y') }}" readonly/>
                                <div class="input-group-addon">TO</div>
                                <input type="text" class="form-control" id="filter_date_to" value="{{ date('m/t/Y') }}" readonly/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm" onclick="clearFilter()">Clear</button>
                <button type="button" class="btn btn-sm btn-theme-sml" onclick="applyFilter()">Terapkan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modalDetail">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                <em class="icon ni ni-cross"></em>
            </a>
            <div class="modal-header">
                <h5 class="modal-title" id="title_modal_filter">Detail Data Penjualan</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="uid" value="">
                <table class="table table-striped nowrap" id="dt-table-detail">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th>Subtotal</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>

                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-theme-sml" class="close" data-bs-dismiss="modal"
                    aria-label="Close">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection