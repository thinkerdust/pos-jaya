@extends('master')

@section('content')

<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-md mx-auto">
                    <div class="nk-block-head nk-block-head-lg wide-sm">
                        <div class="nk-block-head-content">
                            <h2 class="nk-block-title fw-normal">Form Penjualan</h2>
                        </div>
                    </div><!-- .nk-block-head -->
                    <div class="nk-block nk-block-lg">
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <div class="preview-block">
                                    <form class="form-validate is-alter" id="form-data">
                                        @csrf
                                        <input type="hidden" name="uid_sales_order" id="uid_sales_order"
                                            value="{{ isset($uid) ? $uid : null }}">
                                            <input type="hidden" name="invoice_number" id="invoice_number">
                                            <input type="hidden" name="uid_company" id="uid_company" value="{{Auth::user()->uid_company}}">

                                        <div class="row gy-4">
                                            <div class="col-sm-6">

                                                <div class="form-group">
                                                    <label class="form-label">Pelanggan</label>
                                                    <div class="form-control-wrap">
                                                        <select class="form-control" name="customer" id="customer">
                                                        </select>
                                                        <input type="hidden" name="customer_type" id="customer_type">
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
                                                        <input class="form-control" id="address" name="address"/>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="form-label">Prioritas</label>
                                                    <div class="form-control-wrap">
                                                        <select class="form-control" name="priority" id="priority">
                                                            <option value="1">Tinggi</option>
                                                            <option value="2" selected>Normal</option>
                                                            <option value="3">Rendah</option>
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="col-sm-6 pt-3">
                                                <h2 class="text-end mt-3" id="grand_total">Rp. 0,-</h2>
                                                <input type="hidden" name="grand_total" />

                                                <div class="form-group">
                                                    <label class="form-label">Diskon Umum</label>
                                                    <div class="row">
                                                        <div class="form-control-wrap col-md-4">
                                                            <div class="input-group">
                                                                <input type="number" class="form-control" value="0"
                                                                    id="disc_global" name="disc_global"
                                                                    aria-describedby="basic-addon2" max="100">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text"
                                                                        id="basic-addon2">%</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-control-wrap col-md-8">
                                                            <input type="text" class="form-control formated_number"
                                                                id="disc" name="disc"
                                                                value="0" required>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="form-label">PPN</label>
                                                    <div class="row">
                                                        <div class="form-control-wrap col-md-4">
                                                            <div class="input-group">
                                                                <input type="number" class="form-control" value="0"
                                                                    aria-describedby="basic-addon2" id="ppn" name="ppn">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text"
                                                                        id="basic-addon2">%</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-control-wrap col-md-8">
                                                            <input type="text" class="form-control formated_number" id="ppn_value"
                                                                name="ppn_value" value="0" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">Tgl Pengambilan</label>
                                                    <div class="form-control-wrap">
                                                        <input type="date" class="form-control" id="collection_date" value="{{ date('Y-m-d') }}"
                                                            name="collection_date" required>
                                                    </div>
                                                </div>

                                                <!-- <div class="form-group">
                                                    <label class="form-label">Proofing</label>
                                                    <div class="row">
                                                        <div class="form-control-wrap col-md-12"> -->
                                                            <input type="hidden" class="form-control formated_number" id="proofing"
                                                                name="proofing" value="0" >
                                                        <!-- </div>
                                                    </div>
                                                </div> -->

                                                <!-- <div class="form-group">
                                                    <label class="form-label">Keterangan</label>
                                                    <div class="row">
                                                        <div class="form-control-wrap col-md-12"> -->
                                                            <input type="hidden" class="form-control" id="keterangan"
                                                                name="keterangan" >
                                                        <!-- </div>
                                                    </div>
                                                </div> -->

                                            </div>
                                        </div>
                                        <hr class="preview-hr">
                                        <h5 class="mb-2">Produk</h5>
                                        <div class="row gy-4">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="form-label">Produk</label>
                                                    <div class="form-control-wrap">
                                                        <select class="form-control" name="product" id="product">
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="form-label">Harga</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control formated_number" id="price"
                                                            placeholder="0" min="0" name="price">
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
                                                            placeholder="0" min="0">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <label class="form-label">P (cm)</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control formated_number" style="padding:5px" id="panjang" name="panjang"
                                                            value="0">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <label class="form-label">L (cm)</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control formated_number" style="padding:5px" id="lebar" name="lebar"
                                                            value="0">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr class="preview-hr">
                                        <!-- <h5 class="mb-2">Laminasi</h5> -->
                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Keterangan</label>
                                                    <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="notes" name="notes"
                                                            placeholder="notes">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-label">Subtotal</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control formated_number" id="subtotal" name="subtotal"
                                                            placeholder="0" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1 d-flex align-items-end">
                                                <div class="form-group">
                                                    <button class="btn btn-dim btn-outline-secondary" id="add_product"
                                                        type="button"><i class="icon ni ni-plus"></i></button>
                                                </div>
                                            </div>                                                 
                                        </div>
                                        <!-- <hr class="preview-hr"> -->
                                        <!-- <h5 class="mb-2">Cutting & Packing</h5>
                                        <div class="row mt-2">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="form-label">Cutting Price</label>
                                                    <div class="form-control-wrap"> -->
                                                    <input type="hidden" class="form-control formated_number" id="cutting_price"
                                                            placeholder="0" min="0" value="0" name="cutting_price">
                                                    <!-- </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="form-label">Packing Price</label>
                                                    <div class="form-control-wrap"> -->
                                                    <input type="hidden" class="form-control formated_number" id="packing_price"
                                                            placeholder="0" min="0" value="0" name="packing_price">
                                                    <!-- </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Notes</label>
                                                    <div class="form-control-wrap"> -->
                                                        <!-- <input type="text" class="form-control" id="notes" name="notes"
                                                            placeholder="notes"> -->
                                                    <!-- </div>
                                                </div>
                                            </div> -->

                                        <hr class="preview-hr">
                                        <div class="table-responsive mt-4">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="text-center">Nama</th>
                                                        <th class="text-center">Satuan</th>
                                                        <th class="text-center">Stock</th>
                                                        <th class="text-center">Qty</th>
                                                        <th class="text-center">Ukuran</th>
                                                        <th class="text-center">Harga</th>
                                                        <th class="text-center">Subtotal</th>
                                                        <th class="text-center">Notes</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbody_product">
                                                    <tr>
                                                        <td class="text-center text-muted" id="nodata" colspan="11">Tidak
                                                            ada
                                                            product
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <hr class="preview-hr">
                                        @if (Auth::user()->id_role==2)
                                            @if (isset($uid))
                                            <button type="submit" class="btn btn-theme-sml submit"
                                                id="btn-submit">Simpan</button>
                                            @endif
                                        @else
                                            <button type="submit" class="btn btn-theme-sml submit"
                                                    id="btn-submit">Simpan</button>
                                        @endif
                                        @if (empty($uid) || $pending->pending==1)
                                        <button type="submit"
                                            class="btn btn-outline-secondary btn-dim submit" id="btn-pending">Pending</button>
                                        @endif
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