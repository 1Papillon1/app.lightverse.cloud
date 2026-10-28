@extends('layout.app')

@section('title', 'Register')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card text-center">
                <div class="card-body py-5">
                    <i class="bi bi-person-lock display-5 text-muted"></i>
                    <h1 class="h4 mt-3 mb-3">Registration is disabled</h1>
                    <p class="text-muted mb-4">
                        New accounts are created by the administrator. Please contact the administrator to get access.
                    </p>
                    <a href="{{ route('login') }}" class="btn btn-primary">Back to login</a>
                </div>
            </div>
        </div>
    </div>
@endsection
