@extends('layout')

@section('content')
    <h1>Enter Your MailerLite API Key</h1>

    <form action="{{ route('validate_api_key') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="api_key">API Key:</label>
            <input type="text" name="api_key" id="api_key" class="form-control" required value="{{$api_key}}">
            @error('api_key')
                <span class="invalid">Invalid API Key</span>
            @enderror
        </div>
        
        <button type="submit" class="btn btn-primary">Validate API Key</button>
    </form>
@endsection
