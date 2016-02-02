@if (isset($success))
<div class="alert bg-success">
	{{ $success }}
</div>
@endif

@if (isset($error))
<div class="alert alert-danger">
	{{ $error }}
</div>
@endif