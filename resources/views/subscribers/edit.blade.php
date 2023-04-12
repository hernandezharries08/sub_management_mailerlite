@extends('layout')

@section('content')
    <h1>Edit Subscriber</h1>

    <form action="{{ route('subscribers.update', $subscriber['id']) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ $subscriber['email'] }}" disabled>
        </div>

        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $subscriber['fields']['name'] }}" required>
        </div>

        <div class="form-group">
            <label for="country">Country:</label>
            <input type="text" name="country" id="country" class="form-control" value="{{ $subscriber['fields']['country'] }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
@endsection
