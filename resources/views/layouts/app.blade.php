<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/jquery-ui.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <script src="{{asset('js/jquery.min.js')}}"></script>
    <script src="{{asset('js/jquery-ui.js')}}"></script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand d-flex" href="{{ url('/') }}">
                    <div><img src="/images/Top-Tech-News-Logo.png" style="height: 50px; border-right: 1px solid #333;" class="pr-3" alt="TopTech News"></div>
                    <div class="pl-3" style="margin-top: 12px;">Top Tech News</div>
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                            
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ url('/login-activity') }}"><i class="fa fa-btn fa-list"></i>Login Activity</a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @auth
            <div class="container">
                @if(session()->has('success'))
                <div class="alert alert-success">{{session()->get('success')}}</div>
                @endif
                <?php 
                    if(auth()->user()->role==1){
                        $dashUrl = route('admin.dashboard');
                        $plansUrl = route('plans.list');
                    }elseif(auth()->user()->role==2){
                        $dashUrl = route('user.dashboard');
                        $plansUrl = route('plans.index');
                    }
                ?>
                <div class="row">
                    <div class="col-md-2">
                        <ul class="list-group">
                            <li class="list-group-item">
                                <a href="{{ $dashUrl }}">Dashboard</a>
                            </li>
                        <?php if(auth()->user()->role==1){ ?>
                            <li class="list-group-item">
                                <a href="{{route('categories.index')}}">Categories</a>
                            </li>
                        <?php } ?>
                            <li class="list-group-item">
                                <a href="{{ $plansUrl }}">Plans</a>
                            </li>
                            <li class="list-group-item">
                                <a href="{{ route('user.profile') }}">Update Profile</a>
                            </li>
                            <li class="list-group-item">
                                <a href="{{route('change.password_view')}}">Change Password</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-10">
                        @yield('content')
                    </div>
                </div>
            </div>
            @else
                @yield('content')
            @endauth
        </main>
    </div>
@auth
@yield('ajaxJsCode')
@yield('scripts')
@endauth
</body>
</html>
