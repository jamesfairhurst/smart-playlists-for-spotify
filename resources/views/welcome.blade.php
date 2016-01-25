@extends('layouts.app')

@section('content')
<div class="container">

    <div class="jumbotron">
        <h1>Smart Playlists for Spotify</h1>
        <p class="lead">Tired of manually creating Playlists from your saved Tracks? This tool will help you create Smart Playlists based on Track &amp; Album rules. Just login with your Spotify account below to see a full list of all your Tracks and create your first Smart Playlist.</p>
        <p><a href="{{ url('auth/spotify') }}"><img src="{{ asset('img/spotify_buttons/log_in_with_spotify/svg/log_in-desktop.svg') }}" width="30%;"></a></p>
    </div>
    
</div>

<div class="container">
      <!-- Example row of columns -->
      <div class="row">
        <div class="col-md-4">
          <h2>Features</h2>
          <p>Create Smart Playlists with Track/Album rules and these will be automatically created in Spotify from your list of saved Tracks.</p>
          {{-- <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p> --}}
        </div>
        <div class="col-md-4">
          <h2>Todo</h2>
          <ul>
            <li>Use Tracks from all your Playlists not just your saved Tracks</li>
            <li>Live updating in the background so your Playlists are kept upto date</li>
            <li>Additional Playlist Rules</li>
          </ul>
          {{-- <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p> --}}
       </div>
        <div class="col-md-4">
          <h2>About</h2>
          <p>Coming from iTunes and being able to easily create Smart Playlists I became frustrated with not being able to create them in Spotify so I made this.</p>
          <p>All feedback and suggestions welcome just <a href="mailto:info@jamesfairhurst.co.uk">email me</a>.</p>
          {{-- <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p> --}}
        </div>
      </div>
</div>
@endsection
