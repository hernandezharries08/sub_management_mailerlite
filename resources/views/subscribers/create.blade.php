@extends('layout')

@section('content')

    <a href="{{ route('subscribers.index') }}" class="btn btn-primary float-right mb-3">Back to Subscribers</a>

    <h1>Create Subscriber</h1>

    <form action="{{ route('subscribers.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="country">Country:</label>
            <input type="text" name="country" id="country" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Create Subscriber</button>
    </form>

    @if (isset($status))
        <div class="alert alert-primary mt-3" role="alert">
            {{$status}}
        </div>
    @endif

@endsection
