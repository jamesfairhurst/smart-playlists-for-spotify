@extends('layouts.app')

@section('content')
<div class="container">

    @include('common.flash')

    <div class="page-header">
        <h1>Your Tracks</h1>
    </div>

    @if (count($tracks) > 0)
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Artist</th>
                    <th>Album</th>
                    <th>Name</th>
                    <th>Added At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tracks as $track): ?>
                <tr>
                    <td>{{ $track->artist->name }}</td>
                    <td>{{ $track->album->name }}</td>
                    <td>{{ str_limit($track->name, 50) }}</td>
                    <td>{{ $track->added_at->format('d-m-Y H:i') }}</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        {!! $tracks->links() !!}
    @endif

</div>
@endsection
