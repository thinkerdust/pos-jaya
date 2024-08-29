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
                                @can("crudAccess", "TX2")
                                    <a href="/transaction/sales/add" class="btn btn-theme-sml btn-sm"><em
                                            class="icon ni ni-plus"></em><span>Add Data</span></a>
                                    <a id="export_excel" class="btn btn-success btn-sm"><em
                                            class="icon ni ni-download"></em><span>Export Excel</span></a>
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
                                            <th>Total</th>
                                            <th>Note</th>
                                            <th>Status</th>
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
<div class="modal fade" tabindex="-1" id="modal_pembayaran">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                <em class="icon ni ni-cross"></em>
            </a>
            <div class="modal-header">
                <h5 class="modal-title" id="title_modal_pembayaran">Form Pembayaran</h5>
            </div>
            <div class="modal-body">
                <form class="form-validate is-alter" id="form-pembayaran">
                    @csrf
                    <input type="hidden" name="modal_noinv" id="modal_noinv">
                    <input type="hidden" name="modal_uid" id="modal_uid">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Customer</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" name="modal_customer" id="modal_customer"
                                        readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Total</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control formated_number text-end" name="modal_total"
                                        id="modal_total" readonly>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Tanggal</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" name="modal_tanggal" id="modal_tanggal"
                                        readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Sisa Tagihan</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control formated_number text-end"
                                        name="modal_selisih" id="modal_selisih" readonly>
                                </div>
                            </div>

                        </div>
                    </div>

                    <hr class="preview-hr">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Metode Pembayaran</label>
                                <div class="form-control-wrap">
                                    <select class="form-control" name="modal_payment_method" id="modal_payment_method"
                                        required>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Nominal Pembayaran</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control formated_number" name="modal_amount"
                                        placeholder="0" id="modal_amount" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Kembalian</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control formated_number" name="modal_changes"
                                        value="0" id="modal_changes" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-theme-sml" id="modal_submit">Bayar</button>
                        </div>
                    </div>
                </form>
                <hr class="preview-hr">
                <h6>Riwayat Pembayaran</h6>
                <table class="table table-striped nowrap" id="dt-table-price">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Metode Pembayaran</th>
                            <th>Bayar</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tbody_pembayaran"></tbody>

                </table>
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
                                <input type="text" class="form-control" id="filter_date_from" />
                                <div class="input-group-addon">TO</div>
                                <input type="text" class="form-control" id="filter_date_to" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <div class="form-control-wrap">
                                <select class="form-control select2" name="filter_status" id="filter_status">
                                    <option value=""></option>
                                    <option value="1">Lunas</option>
                                    <option value="0">Belum Lunas</option>
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