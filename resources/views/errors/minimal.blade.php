@extends('layout.app')

{{-- Zajednički predložak za sve error stranice (403, 404, 419, 429, 500, 503) prilagođen layoutu aplikacije --}}

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body py-5">
                    <p class="display-4 text-muted mb-2">@yield('code')</p>
                    <h1 class="h4 mb-3">@yield('message')</h1>
                    <p class="text-muted mb-4">@yield('description')</p>

                    <a href="{{ route('dashboard') }}" class="btn btn-primary">Back to home</a>
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Go back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
