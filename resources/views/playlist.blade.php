@extends('layouts.app')

@section('content')
<div class="container">

    @include('common.flash')

    <div class="page-header">
        <h1>Your <span class="text-muted">{{ $playlist->name }}</span> Playlist</h1>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Rules &amp; Info</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($playlist->rules as $rule)
            <tr>
                <td>{{ $rule->key_nice }} <span class="text-muted">{{ $rule->comparison_operator_nice }}</span> <strong>{{ $rule->value }}</strong></td>
            </tr>
            @endforeach
            @if ($playlist->order_nice)
            <tr>
                <td>Order by <strong>{{ $playlist->order_nice }}</strong></td>
            </tr>
            @endif
            @if ($playlist->limit)
            <tr>
                <td>Limit <strong>{{ ($playlist->limit) ? $playlist->limit : 'n/a' }}</strong></td>
            </tr>
            @endif
        </tbody>
    </table>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Track</th>
                <th>Artist</th>
                <th>Album</th>
                <th>Added At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tracks as $track): ?>
            <tr>
                <td>{{ str_limit($track->name, 50) }}</td>
                <td>{{ $track->artist->name }}</td>
                <td>{!! $track->album->name . '<br/><small>' . $track->album->released_at->format('m/Y') . '</small>' !!}</td>
                <td>{{ $track->added_at->format('d-m-Y H:i') }}</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>
@endsection
