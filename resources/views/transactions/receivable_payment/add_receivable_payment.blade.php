@extends('master')

@section('content')

<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-md mx-auto">
                    <div class="nk-block-head nk-block-head-lg wide-sm">
                        <div class="nk-block-head-content">
                            <h2 class="nk-block-title fw-normal">Form Penerimaan</h2>
                        </div>
                    </div><!-- .nk-block-head -->
                    <div class="nk-block nk-block-lg">
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <div class="preview-block">
                                    <form class="form-validate is-alter" id="form-data">
                                        @csrf
                                        <input type="hidden" name="uid_payment" id="uid_payment"
                                            value="{{ isset($uid) ? $uid : null }}">

                                        <div class="row gy-4">
                                            <div class="col-sm-6">

                                                <div class="form-group">
                                                    <label class="form-label">No. Invoice</label>
                                                    <div class="form-control-wrap">
                                                        <input class="form-control" name="invoice_number"
                                                            id="invoice_number" required>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="form-label">Customer</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="customer"
                                                            name="customer" readonly>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="form-label">Metode Pembayaran</label>
                                                    <div class="form-control-wrap">
                                                        <select class="form-control" id="payment_method"
                                                            name="payment_method" required></select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="form-label">Nominal</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control formated_number"
                                                            id="nominal" name="nominal" required>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="col-sm-6">
                                                <h2 class="text-end mt-3" id="grand_total">Rp. 0,-</h2>
                                                <input type="hidden" name="grand_total" />

                                                <div class="form-group">
                                                    <label class="form-label">
                                                        <input class="form-check-input" type="checkbox" id="en_disc">
                                                        Diskon</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control formated_number"
                                                            id="disc" name="disc" value="0" required disbaled>
                                                    </div>
                                                </div>
                                                <!-- <div class="form-group">
                                                    <label class="form-label">Subtotal</label>
                                                    <div class="form-control-wrap">
                                                        <input type="number" class="form-control" id="subtotal"
                                                            name="subtotal" min="0" value="0" required disabled>
                                                    </div>
                                                </div> -->


                                            </div>
                                        </div>
                                        <hr class="preview-hr">
                                        <div class="row gy-4">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-label">Produk</label>
                                                    <div class="form-control-wrap">
                                                        <select class="form-control" name="material" id="material">
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="form-label">Harga</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control formated_number"
                                                            id="price" placeholder="0" name="price" value="0">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="form-label">Satuan</label>
                                                    <div class="form-control-wrap">
                                                        <select class="form-control" name="unit" id="unit">
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="form-label">Qty</label>
                                                    <div class="form-control-wrap">
                                                        <input type="number" class="form-control" id="qty" name="qty"
                                                            placeholder="0" min="0" value="0">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <div class="form-group">
                                                    <button class="btn btn-light" type="button"
                                                        id="add_material">Tambah</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive mt-4">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="text-center">Nama</th>
                                                        <th class="text-center">Satuan</th>
                                                        <th class="text-center">Harga</th>
                                                        <th class="text-center">Qty</th>
                                                        <th class="text-center">Subtotal</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbody_material">
                                                    <tr>
                                                        <td class="text-center text-muted" id="nodata" colspan="6">Tidak
                                                            ada
                                                            produk
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <hr class="preview-hr">
                                        <button type="submit" class="btn btn-theme-sml" id="btn-submit">Simpan</button>
                                    </form>
                                </div>
                            </div>
                        </div><!-- .card-preview -->
                    </div><!-- .nk-block -->
                </div><!-- .components-preview -->
            </div>
        </div>
    </div>
</div>
@endsection