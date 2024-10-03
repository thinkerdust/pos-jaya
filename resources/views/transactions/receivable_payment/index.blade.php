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
<div class="modal fade" tabindex="-1" id="modal_filter">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                <em class="icon ni ni-cross"></em>
            </a>
            <div class="modal-header">
                <h5 class="modal-title" id="title_modal_filter">Filter Pembayaran</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <label class="form-label">Datepicker Range</label>
                        <div class="form-control-wrap">
                            <div class="input-daterange date-picker-range input-group">
                                <input type="text" class="form-control" id="filter_date_from" value="" readonly/>
                                <div class="input-group-addon">TO</div>
                                <input type="text" class="form-control" id="filter_date_to" value="" readonly/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">Metode Pembayaran</label>
                            <div class="form-control-wrap">
                                <select class="form-control" name="modal_payment_method" id="modal_payment_method">
                                </select>
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
@endsection