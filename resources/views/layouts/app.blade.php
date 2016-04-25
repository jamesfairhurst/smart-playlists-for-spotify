<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Smart Playlists for Spotify</title>

    <!-- Fonts -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700" rel='stylesheet' type='text/css'>

    <!-- Styles -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}
</head>
<body id="app-layout">
    <nav class="navbar navbar-default">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <a class="navbar-brand" href="{{ url('/') }}">
                    Smart Playlists for Spotify
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                @if ( ! Auth::guest())
                <ul class="nav navbar-nav">
                    <li{!! (Request::is('tracks')) ? ' class="active"' : '' !!}><a href="{{ url('/tracks') }}"> <i class="fa fa-music"></i> Tracks</a></li>
                    <li{!! (Request::is('playlists')) ? ' class="active"' : '' !!}><a href="{{ url('/playlists') }}"><i class="fa fa-list"></i> Playlists</a></li>
                    <li><a href="mailto:support@smartplaylistsforspotify.co.uk"><i class="fa fa-envelope-o"></i> Support</a></li>
                </ul>
                @endif

                <ul class="nav navbar-nav navbar-right">
                    @if (!Auth::guest())
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/auth/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')

    <footer class="footer">
        <div class="container">
            <p class="text-muted text-center">Made with <a href="https://laravel.com" target="_blank">Laravel</a> on <a href="https://forge.laravel.com" target="_blank">Forge</a> by <a href="http://www.jamesfairhurst.co.uk" target="_blank">James Fairhurst</a> fueled by <a href="https://open.spotify.com/user/james_f" target="_blank"><i class="fa fa-music"></i></a> <a href="https://pactcoffee.com/?voucher=JAMES-XIKO8G" target="_blank"><i class="fa fa-coffee"></i></a> <a href="http://www.bakingbad.co.uk/2013/07/my-favourite-carrot-cake/" target="_blank"><i class="fa fa-birthday-cake"></i></a> <a href="http://www.leffe.com/en/beers/leffe-blond" target="_blank"><i class="fa fa-beer"></i></a></p>
        </div>
    </footer>

    <!-- JavaScripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
</body>
</html>
