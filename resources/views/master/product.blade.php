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
                                @can("crudAccess", "MD3")
                                <a href="" onclick="tambah()" class="toggle btn btn-theme-sml btn-sm"><em class="icon ni ni-plus"></em><span>Add Data</span></a>
                                @endcan
                                <hr class="preview-hr">
                                <table class="table table-striped nowrap" id="dt-table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th> 
                                            <th>Kategori</th>
                                            <th>Satuan</th>
                                            <th>Harga Produk</th>
                                            <th>Harga Jual</th>
                                            <th>Harga Member Retail</th>
                                            <th>Stock</th>
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

<!-- Modal Content Code -->
<div class="modal fade" tabindex="-1" id="modalForm">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                <em class="icon ni ni-cross"></em>
            </a>
            <div class="modal-header">
                <h5 class="modal-title">Form Produk</h5>
            </div>
            <div class="modal-body">
                <form class="form-validate is-alter" id="form-data">
                    @csrf
                    <input type="hidden" name="uid" id="uid">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Nama</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" name="name" id="name" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Kategori</label>
                                <div class="form-control-wrap">
                                    <select class="form-control" name="product_categories" id="product_categories" required></select>
                                </div>
                                <div class="my-1">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="addCategory()"><em class="icon ni ni-plus"></em><span>Add Data</span></button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Satuan</label>
                                <div class="form-control-wrap">
                                    <select class="form-control" name="unit" id="unit" required></select>
                                </div>
                                <div class="my-1">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="addUnit()"><em class="icon ni ni-plus"></em><span>Add Data</span></button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Keterangan</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control" rows="5" name="description" id="description"></textarea>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Harga Pokok</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control format-number" name="cost_price" id="cost_price" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Harga Jual</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control format-number" name="sell_price" id="sell_price" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Harga Member Retail</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control format-number" name="retail_member_price" id="retail_member_price" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Stock</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control format-number" name="stock" id="stock" value="0" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="preview-hr">
                    <button type="submit" class="btn btn-theme-sml" id="btn-submit">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modalFormPrice">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                <em class="icon ni ni-cross"></em>
            </a>
            <div class="modal-header">
                <h5 class="modal-title">Form Produk - Harga Grosir</h5>
            </div>
            <div class="modal-body">
                <form class="form-validate is-alter" id="form-data-price">
                    @csrf
                    <input type="hidden" name="uid_product" id="uid_product">
                    <input type="hidden" name="uid_price" id="uid_price">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Qty 1</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control format-number text-end" name="first_quantity" id="first_quantity" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                    <label class="form-label">Qty 2</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control format-number text-end" name="last_quantity" id="last_quantity" required>
                                    </div>
                                </div>
                            </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Harga</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control format-number text-end" name="price" id="price" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="preview-hr">
                    <button type="submit" class="btn btn-theme-sml" id="btn-submit">Save</button>
                </form>
                <hr class="preview-hr">
                <table class="table table-striped nowrap" id="dt-table-price">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Qty Pertama</th> 
                            <th>Qty Terakhir</th>
                            <th>Harga</th>
                            <th>Status</th> 
                            <th>Action</th> 
                        </tr>
                    </thead>

                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modalFormCategory">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                <em class="icon ni ni-cross"></em>
            </a>
            <div class="modal-header">
                <h5 class="modal-title">Form Produk Kategori</h5>
            </div>
            <div class="modal-body">
                <form class="form-validate is-alter" id="form-data-category">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Nama</label>
                        <div class="form-control-wrap">
                            <input type="text" class="form-control" name="name_category" id="name_category" required>
                        </div>
                    </div>
                    
                    <hr class="preview-hr">
                    <button type="button" class="btn btn-theme-sml" id="btn-submit-category">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modalFormUnit">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                <em class="icon ni ni-cross"></em>
            </a>
            <div class="modal-header">
                <h5 class="modal-title">Form Satuan</h5>
            </div>
            <div class="modal-body">
                <form class="form-validate is-alter" id="form-data-unit">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Nama</label>
                        <div class="form-control-wrap">
                            <input type="text" class="form-control" name="name_unit" id="name_unit" required>
                        </div>
                    </div>
                    
                    <hr class="preview-hr">
                    <button type="button" class="btn btn-theme-sml" id="btn-submit-unit">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style type="text/css">
    .modal.show .select2-container {
        position: inherit !important;
    }
</style>

@endsection