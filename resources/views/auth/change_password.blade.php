@extends('master')

@section('content')
<style>
    .form-icon-right{
        left: auto;
        right: 10px !important;
    }
</style>

<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">{{ $title }}</h3>
                        </div>
                    </div><!-- .nk-block-between -->
                </div><!-- .nk-block-head -->
                <div class="nk-block">
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            <div class="preview-block">
                                <div class="row gy-4">
                                    <form class="form-validate is-alter" id="form-data">
                                        @csrf
                                        <div class="form-group row">
                                            <label class="form-label col-md-2">Current Password</label>
                                            <div class="form-control-wrap col-md-10">
                                                <a href="#" class="form-icon form-icon-right passcode-switch" data-target="old_password">
                                                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                                </a>
                                                <input type="password" name="old_password" id="old_password" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="form-label col-md-2">New Password</label>
                                            <div class="col-md-10">
                                                <a href="#" class="form-icon form-icon-right passcode-switch" data-target="password">
                                                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                                </a>
                                                <input type="password" name="password" id="password" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="form-label col-md-2">Confirm Password</label>
                                            <div class="col-md-10">
                                                <a href="#" class="form-icon form-icon-right passcode-switch" data-target="re_password">
                                                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                                </a>
                                                <input type="password" name="re_password" id="re_password" class="form-control" required>
                                            </div>
                                        </div>
                                        <hr class="preview-hr">
                                        <button type="submit" class="btn btn-theme-sml" id="btn-submit">Save</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div><!-- .card-preview -->
                </div><!-- .nk-block -->
            </div>
        </div>
    </div>
</div>
@endsection