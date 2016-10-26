@extends('layouts.app')

@section('content')
<div class="container">

    <div class="jumbotron">
        <h1>Smart Playlists for Spotify</h1>
        <p class="lead">Tired of manually creating Playlists from your saved Tracks? This tool will help you create Smart Playlists based on Track &amp; Album rules. Just login with your Spotify account below to see a full list of all your Tracks and create your first Smart Playlist.</p>
        <p style="margin: 0;"><a href="{{ url('auth/spotify') }}"><img src="{{ asset('img/spotify_buttons/log_in_with_spotify/svg/log_in-desktop.svg') }}" width="30%;"></a></p>
    </div>
    
</div>

<div class="container">
  <!-- Example row of columns -->
  <div class="row">
    <div class="col-md-4 text-center">
      <h2>Features</h2>
      <p>Create Smart Playlists with Track/Album rules and push them to Spotify from your list of saved Tracks.</p>
      <div class="row">
        <div class="col-xs-6 col-md-4">
          <a href="{{ asset('img/screenshot-01.png') }}" class="thumbnail">
            <img src="{{ asset('img/screenshot-01-thumbnail.png') }}" class="img-responsive img-rounded" alt="">
          </a>
        </div>
        <div class="col-xs-6 col-md-4">
          <a href="{{ asset('img/screenshot-02.png') }}" class="thumbnail">
            <img src="{{ asset('img/screenshot-02-thumbnail.png') }}" class="img-responsive img-rounded" alt="">
          </a>
        </div>
        <div class="col-xs-6 col-md-4">
          <a href="{{ asset('img/screenshot-03.png') }}" class="thumbnail">
            <img src="{{ asset('img/screenshot-03-thumbnail.png') }}" class="img-responsive img-rounded" alt="">
          </a>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <h2>About</h2>
      <p>Coming from iTunes and being able to easily create Smart Playlists I became frustrated with not being able to create them in Spotify so I made this.</p>
      <p>The app asks for the <strong>playlist-modify-public</strong> &amp; <strong>user-library-read</strong> <a href="https://developer.spotify.com/web-api/using-scopes/">scopes</a> which is pretty restrictive and the bare minimum required to read your Tracks and create Playlists from them.</p>
      <p>All feedback and suggestions welcome just <a href="mailto:support@smartplaylistsforspotify.co.uk">email me</a> or open an issue or pull request on <a href="https://github.com/jamesfairhurst/smart-playlists-for-spotify">Github</a>.</p>
    </div>
  </div>
</div>
@endsection
