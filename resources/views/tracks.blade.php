@extends('layouts.app')

@section('content')
<div class="container">

    @include('common.flash')

    <div class="page-header">
        <h1>Your Tracks</h1>
    </div>

    @if (count($tracks) > 0)
        <table class="table table-striped">
            <col/>
            <col/>
            <col/>
            <col width="100px" />
            <thead>
                <tr>
                    <th>Artist</th>
                    <th>Album</th>
                    <th>Name</th>
                    <th class="text-center">Added At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tracks as $track)
                <tr>
                    <td>{{ $track->artist->name }}</td>
                    <td>{{ $track->album->name }}</td>
                    <td>{{ str_limit($track->name, 50) }}</td>
                    <td class="text-center">{{ $track->added_at->format('d-m-Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {!! $tracks->links() !!}
    @else
        <div class="alert alert-info text-center" role="alert">
            <p>Your Tracks are being retrieved from Spotify in the background, they'll appear here once done. In the meantime <a href="{{ url('playlists') }}">create a Playlist</a>.</p>
        </div>
    @endif

</div>
@endsection
