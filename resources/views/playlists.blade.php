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

                <div class="form-group filter-rule-row">
                    <label for="playlist-name" class="col-sm-3 control-label">Rule</label>

                    <div class="col-sm-3">
                        <select name="rule[0][key]" class="form-control">
                            <option value="artist">Artist</option>
                            <option value="album">Album</option>
                            {{-- <option value="genre">Genre</option> --}}
                            <option value="date_added">Date Added</option>
                            {{-- <option value="plays">Plays</option> --}}
                            <option value="year">Year</option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <select name="rule[0][comparison_operator]" class="form-control">
                            <option value="contains">contains</option>
                            <option value="not_contains">does not contain</option>
                            <option value="=">is</option>
                            <option value="!=">is not</option>
                            <option value="begins_with">begins with</option>
                            <option value="ends_with">ends with</option>
                            <option value=">">is greater than</option>
                            <option value="<">is less than</option>
                            <option value=">">is after</option>
                            <option value="<">is before</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <input type="text" name="rule[0][value]" class="form-control">
                    </div>
                    <div class="col-sm-1">
                        <button type="button" class="btn btn-default btn-success" aria-label="Left Align">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                        </button>
                        <button type="button" class="btn btn-default btn-danger hidden" aria-label="Left Align">
                            <span class="glyphicon glyphicon-minus" aria-hidden="true"></span>
                        </button>
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
