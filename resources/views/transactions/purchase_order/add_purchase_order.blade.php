@extends('master')

@section('content')

<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-md mx-auto">
                    <div class="nk-block-head nk-block-head-lg wide-sm">
                        <div class="nk-block-head-content">
                            <h2 class="nk-block-title fw-normal">Form Pembelian</h2>
                        </div>
                    </div><!-- .nk-block-head -->
                    <div class="nk-block nk-block-lg">
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <div class="preview-block">
                                    <form class="form-validate is-alter" id="form-data">
                                        @csrf
                                        <input type="hidden" name="uid_purchase_order" id="uid_purchase_order"
                                            value="{{ isset($uid) ? $uid : null }}">
                                        <div class="row gy-4">
                                            <div class="col-sm-6">

                                                <div class="form-group">
                                                    <label class="form-label">Supplier</label>
                                                    <div class="form-control-wrap">
                                                        <select class="form-control" name="supplier" id="supplier">
                                                            <option value="">-- Pilih Supplier --</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="form-label">No Telp</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="phone" name="phone"
                                                            required>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="form-label">Alamat</label>
                                                    <div class="form-control-wrap">
                                                        <textarea class="form-control" id="address" name="address"
                                                            rows="5" required></textarea>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="col-sm-6">
                                                <h1 class="text-end mt-3" id="grand_total">Rp. 0,-</h1>
                                                <input type="hidden" name="grand_total" />

                                                <div class="form-group">
                                                    <label class="form-label">
                                                        <input class="form-check-input" type="checkbox" id="en_disc">
                                                        Diskon</label>
                                                    <div class="form-control-wrap">
                                                        <input type="number" class="form-control" id="disc" name="disc"
                                                            min="0" value="0" required disbaled>
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
                                                        <select class="form-control" name="produk" id="produk">
                                                            <option value="">-- Pilih Produk --</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="form-label">Harga</label>
                                                    <div class="form-control-wrap">
                                                        <input type="number" class="form-control" id="price"
                                                            placeholder="0" min="0" name="price">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="form-label">Satuan</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="uom" name="uom"
                                                            placeholder="pcs">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="form-label">Qty</label>
                                                    <div class="form-control-wrap">
                                                        <input type="number" class="form-control" id="qty" name="qty"
                                                            placeholder="0" min="0">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <div class="form-group">
                                                    <button class="btn btn-light" type="button">Tambah</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive mt-4">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Nama</th>
                                                        <th>Jenis</th>
                                                        <th>Harga</th>
                                                        <th>Qty</th>
                                                        <th>Subtotal</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Produk A</td>
                                                        <td>Consumable</td>
                                                        <td class="text-end">1.000.000</td>
                                                        <td class="text-end">2</td>
                                                        <td class="text-end">2.000.000</td>
                                                        <td class="text-center">
                                                            <a class="btn btn-sm btn-dim btn-outline-secondary"
                                                                type="button"><em class="icon ni ni-trash"></em>
                                                                Delete</a>
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