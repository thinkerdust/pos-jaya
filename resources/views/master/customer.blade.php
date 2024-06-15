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
                                @can("crudAccess", "MD2")
                                <a href="" onclick="tambah()" class="toggle btn btn-theme-sml btn-sm"><em class="icon ni ni-plus"></em><span>Add Data</span></a>
                                @endcan
                                <hr class="preview-hr">
                                <table class="table table-striped nowrap" id="dt-table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th> 
                                            <th>Organisasi</th> 
                                            <th>Phone</th> 
                                            <th>Email</th> 
                                            <th>Alamat</th> 
                                            <th>Tipe</th> 
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
                <h5 class="modal-title">Form Pelanggan</h5>
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
                        <label class="form-label">Organisasi</label>
                        <div class="form-control-wrap">
                            <input type="text" class="form-control" name="organisation" id="organisation" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <div class="form-control-wrap">
                            <input type="text" class="form-control" name="phone" id="phone" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alamat</label>
                        <div class="form-control-wrap">
                            <textarea class="form-control" name="address" id="address" rows="5" required></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <div class="form-control-wrap">
                            <input type="email" class="form-control" name="email" id="email" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tipe</label>
                        <div class="form-control-wrap">
                            <select class="form-control" name="type" id="type" required>
                                <option value=""></option>
                                <option value="Retail">Retail</option>
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