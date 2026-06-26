{{-- Controllers use with('success', ...) to flash success messages into the
    session for exactly one request. This component renders that feedback. --}}
@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- Laravel validation failures redirect back with an error bag in the session.
    Showing this block tells the user the form was rejected before saving. --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Please check the form and try again.</strong>
    </div>
@endif
