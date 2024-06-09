@extends('master')

@section('content')

<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Welcome, {{ Auth::user()->username }}</h3>
                        </div><!-- .nk-block-head-content -->
                        
                    </div><!-- .nk-block-between -->
                </div><!-- .nk-block-head -->
                
            </div>
        </div>
    </div>
</div>

@endsection