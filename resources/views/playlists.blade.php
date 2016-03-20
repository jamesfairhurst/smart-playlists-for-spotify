@extends('layouts.app')

@section('content')
<div class="container">

    @include('common.flash')

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
                        <input type="text" name="name" id="playlist-name" class="form-control" value="{{ old('name') }}" placeholder="This is not The Greatest Playlist in the World...">
                    </div>
                </div>

                <div class="form-group filter-rule-row">
                    <label for="playlist-name" class="col-sm-3 control-label">Rule</label>

                    <div class="col-sm-3">
                        <select name="rule[0][key]" class="form-control">
                            <option value="artist">Artist</option>
                            <option value="album">Album</option>
                            {{-- <option value="genre">Genre</option> --}}
                            {{-- <option value="plays">Plays</option> --}}
                            <option value="year">Year</option>
                            <option value="date_added">Date Added</option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <select name="rule[0][comparison_operator]" class="form-control">
                            <option value="contains">contains</option>{{-- artist|album --}}
                            <option value="not_contains">does not contain</option>{{-- artist|album --}}
                            <option value="=">is</option>{{-- artist|album|date_added|year --}}
                            <option value="!=">is not</option>{{-- artist|album|date_added|year --}}
                            <option value="begins_with">begins with</option>{{-- artist|album --}}
                            <option value="ends_with">ends with</option>{{-- artist|album --}}
                            <option value=">">is greater than</option>{{-- year --}}
                            <option value="<">is less than</option>{{-- year --}}
                            <option value=">">is after</option>{{-- date_added --}}
                            <option value="<">is before</option>{{-- date_added --}}
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <input type="text" name="rule[0][value]" class="form-control" placeholder="Tenacious D">
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
                    <label for="playlist-limit" class="col-sm-3 control-label">Limit?</label>

                    <div class="col-sm-6">
                        <input type="text" name="limit" id="playlist-limit" class="form-control" value="{{ old('limit') }}" placeholder="Leave blank for no limit">
                    </div>
                </div>

                <div class="form-group">
                    <label for="playlist-order" class="col-sm-3 control-label">Order</label>

                    <div class="col-sm-6">
                        {{-- <input type="text" name="order" id="playlist-order" class="form-control" value="{{ old('order') }}"> --}}
                        <select name="order" id="playlist-order" class="form-control">
                            <option value="added_desc">Date Added Desc</option>
                            <option value="added_asc">Date Added Asc</option>
                            <option value="album">Album</option>
                            <option value="artist">Artist</option>
                            <option value="name">Name</option>
                            <option value="year_desc">Year Desc</option>
                            <option value="year_asc">Year Asc</option>
                            <option value="random">Random</option>
                            {{-- <option value="popularity_desc">Popularity Desc</option> --}}
                            {{-- <option value="popularity_asc">Popularity Asc</option> --}}
                            {{-- <option value="played_desc">Played Desc</option> --}}
                            {{-- <option value="played_asc">Played Asc</option> --}}
                            {{-- <option value="genre">Genre</option> --}}
                        </select>
                    </div>
                </div>

                {{-- <div class="form-group">
                    <label for="playlist-live" class="col-sm-3 control-label">Live updating?</label>

                    <div class="col-sm-6">
                        <input type="checkbox" name="live" id="playlist-live">
                    </div>
                </div> --}}

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
                    <td class="col-xs-3">{{ $playlist->created_at->format('d-m-Y H:i') }}</td>
                    <td class="col-xs-4 col-md-3">
                        <a href="{{ url('/playlist', [$playlist->id]) }}" class="btn btn-default">View</a>
                        <form action="{{ url('playlist/' . $playlist->id) }}" method="POST" class="visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
                            {!! csrf_field() !!}

                            <button class="btn btn-default">Push</button>
                        </form>
                        <form action="{{ url('playlist/' . $playlist->id) }}" method="POST" class="visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
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
