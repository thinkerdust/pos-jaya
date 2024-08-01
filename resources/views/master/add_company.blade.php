@extends('master')

@section('content')

<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-md mx-auto">
                    <div class="nk-block-head nk-block-head-lg wide-sm">
                        <div class="nk-block-head-content">
                            <h2 class="nk-block-title fw-normal">Form Perusahaan</h2>
                        </div>
                    </div><!-- .nk-block-head -->
                    <div class="nk-block nk-block-lg">
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <div class="preview-block">
                                    <form class="form-validate is-alter" id="form-data">
                                        @csrf
                                        <input type="hidden" name="uid" id="uid" value="{{ isset($uid) ? $uid:null }}">
                                    <div class="row gy-4">
                                        <div class="col-sm-6">
                                            
                                            <div class="form-group">
                                                <label class="form-label">Nama</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="name" name="name" required>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="form-label">No Telp</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="phone" name="phone" required>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="form-label">Alamat</label>
                                                <div class="form-control-wrap">
                                                    <textarea class="form-control" id="address" name="address" rows="5" required></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="form-label">Atas Nama Rekening</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="account_name" name="account_name">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="form-label">Nomor Rekening</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="account_number" name="account_number">
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label">Foto Logo</label>
                                            <label class="cabinet center-block">
                                            <figure>
                                                <img src="" class="gambar img-responsive img-thumbnail" id="preview_image" />
                                                <figcaption>
                                                    <ul>
                                                        <li>*)Leave blank if you don't want to replace</li>
                                                        <li>*)Max size file 5 MB</li>
                                                    </ul>
                                                </figcaption>
                                            </figure>
                                            <div class="form-control-wrap">
                                                <div class="form-file">
                                                    <input type="file" class="form-file-input" id="photo" name="photo" accept=".png, .jpg">
                                                    <label class="form-file-label" for="photo">Choose file</label>
                                                    <a href="" target="_blank" id="filename_photo"></a>
                                                    
                                                </div>
                                            </div>
                                            </label>
                                        </div>
                                    </div>
                                    <hr class="preview-hr">
                                    <button type="submit" class="btn btn-theme-sml" id="btn-submit">Save</button>
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

<style type="text/css">
    label.cabinet{
        display: block;
        cursor: pointer;
    }

    label.cabinet input.file{
        position: relative;
        height: 100%;
        width: auto;
        opacity: 0;
        -moz-opacity: 0;
        filter:progid:DXImageTransform.Microsoft.Alpha(opacity=0);
        margin-top:-30px;
    }

    .gambar {
        width: 200px;
        height: 200px;
        object-fit: cover;
        object-position: 50% 0;
    }
</style>

@endsection