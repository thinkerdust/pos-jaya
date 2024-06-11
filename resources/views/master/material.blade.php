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
                                            <th>Satuan</th> 
                                            <th>Isi</th> 
                                            <th>Harga Beli</th> 
                                            <th>Stok</th> 
                                            <th>Supplier</th> 
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

<!-- Modal Content Code -->
<div class="modal fade" tabindex="-1" id="modalForm">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                <em class="icon ni ni-cross"></em>
            </a>
            <div class="modal-header">
                <h5 class="modal-title">Form Bahan</h5>
            </div>
            <div class="modal-body">
                <form class="form-validate is-alter" id="form-data">
                    @csrf
                    <input type="hidden" name="uid" id="uid">
                    <div class="form-group">
                        <label class="form-label">Nama</label>
                        <div class="form-control-wrap">
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Satuan</label>
                        <div class="form-control-wrap">
                            <select class="form-control" name="unit" id="unit" required>

                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Isi</label>
                        <div class="form-control-wrap">
                            <input type="text" class="form-control number" name="volume" id="volume" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Ukuran ( P x L )</label>
                        <div class="row d-flex align-items-center">
                            <div class="col-md-3">
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control number" name="length" id="length" value="1" required>
                                </div>
                            </div>
                            <div class="col-md-1 text-center">
                                <span class="fw-bold">X</span>
                            </div>
                            <div class="col-md-3">
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control number" name="wide" id="wide" value="1" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Harga Beli</label>
                        <div class="form-control-wrap">
                            <input type="text" class="form-control number" name="price" id="price" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Stok</label>
                        <div class="form-control-wrap">
                            <input type="text" class="form-control number" name="stock" id="stock" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Supplier</label>
                        <div class="form-control-wrap">
                            <select class="form-control" name="supplier" id="supplier" required>

                            </select>
                        </div>
                    </div>
                    
                    <hr class="preview-hr">
                    <button type="submit" class="btn btn-theme-sml" id="btn-submit">Save</button>
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