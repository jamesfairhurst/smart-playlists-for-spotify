@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Your Playlists</h1>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">New Playlist</div>

        <div class="panel-body">
            @include('common.errors')

            <form action="/playlist" method="POST" class="form-horizontal">
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="playlist-name" class="col-sm-3 control-label">Name</label>

                    <div class="col-sm-6">
                        <input type="text" name="name" id="playlist-name" class="form-control" value="{{ old('name') }}">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-6">
                        <button type="submit" class="btn btn-default btn-primary">
                            <i class="fa fa-plus"></i> Add Playlist
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if (count($playlists) > 0)
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Created At</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($playlists as $playlist): ?>
                <tr>
                    <td>{{ str_limit($playlist->name, 50) }}</td>
                    <td>{{ $playlist->created_at->format('d-m-Y H:i') }}</td>
                    <td>
                        <form action="{{ url('playlist/' . $playlist->id) }}" method="POST">
                            {!! csrf_field() !!}
                            {!! method_field('DELETE') !!}

                            <button class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        {!! $playlists->links() !!}
    @endif

</div>
@endsection
