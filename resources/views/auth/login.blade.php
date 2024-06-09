<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="js">

<head>
    <base href="../">
    <meta charset="utf-8">
    <meta name="author" content="ForYou by Sinarmas land">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="ForYou by Sinarmas land">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="{{ asset('images/icon-bag-small.png') }}">
    <!-- Page Title  -->
    <title>Login | {{ config('app.name', 'Laravel') }}</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="{{ asset('assets/css/dashlite.css?ver=3.1.0') }}">
    <link id="skin-default" rel="stylesheet" href="{{ asset('assets/css/theme.css?ver=3.1.0') }}">
</head>

<body class="nk-body bg-white npc-default pg-auth">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- wrap @s -->
            <div class="nk-wrap nk-wrap-nosidebar">
                <!-- content @s -->
                <div class="nk-content ">
                    <div class="nk-block nk-block-middle nk-auth-body  wide-xs">
                        <div class="brand-logo pb-4 text-center">
                            <!-- <a href="/" class="logo-link">
                                <img class="logo-light logo-img logo-img-lg" src="{{ asset('images/icon-bag-small-2.png') }}" srcset="{{ asset('images/icon-bag-small-2.png 2x') }}" alt="logo">
                                <img class="logo-dark logo-img logo-img-lg" src="{{ asset('images/icon-bag-small-2.png') }}" srcset="{{ asset('images/icon-bag-small-2.png 2x') }}" alt="logo-dark">
                            </a> -->
                        </div>
                        <div class="card">
                            <div class="card-inner card-inner-lg">
                                <div class="nk-block-head">
                                    <div class="nk-block-head-content">
                                        <h4 class="nk-block-title">Sign-In</h4>
                                        <div class="nk-block-des">
                                            <p>Welcome to POS JAYA</p>
                                        </div>
                                    </div>
                                </div>
                                <form class="form-validate is-alter" id="form-data">
                                    @csrf
                                    <div class="form-group">
                                        <div class="form-label-group">
                                            <label class="form-label" for="default-01">Username</label>
                                        </div>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control form-control-lg" id="username" name="username" placeholder="Enter your username" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-label-group">
                                            <label class="form-label" for="password">Password</label>
                                        </div>
                                        <div class="form-control-wrap">
                                            <a href="#" class="form-icon form-icon-right passcode-switch lg" data-target="password">
                                                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                            </a>
                                            <input type="password" class="form-control form-control-lg" name="password" id="password" placeholder="Enter your passcode" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-lg btn-theme-sml btn-block" id="btn-submit">Sign in</button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- wrap @e -->
            </div>
            <!-- content @e -->
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->
    <!-- JavaScript -->
    <script src="{{ asset('assets/js/bundle.js?ver=3.1.0') }}"></script>
    <script src="{{ asset('assets/js/scripts.js?ver=3.1.0') }}"></script>

    <script type="text/javascript">
        const token = $("meta[name='csrf-token']").attr("content");
    </script>

    <!-- custom js -->
    @if(isset($js))
    <script src="{{ asset($js) }}"></script>
    @endif
</html>