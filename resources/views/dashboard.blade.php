@extends('layout.app')

@section('title', 'Dashboard')

@section('content')
    <h1 class="h3 mb-3">Dashboard</h1>

    <div class="row g-3">
        @if(auth()->user()->hasRole(\App\Models\Role::SUPER_ADMIN))
            {{-- Super admin: sve datoteke i svi useri --}}
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h2 class="h5 card-title"><i class="bi bi-files me-2"></i>All files</h2>
                        <p class="display-6 mb-3">{{ $filesCount }}</p>
                        <a href="{{ route('admin.files') }}" class="btn btn-outline-primary btn-sm">View all files</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h2 class="h5 card-title"><i class="bi bi-people me-2"></i>Users</h2>
                        <p class="display-6 mb-3">{{ $usersCount }}</p>
                        <a href="{{ route('admin.users') }}" class="btn btn-outline-primary btn-sm">View users</a>
                    </div>
                </div>
            </div>
        @else
            @php
                $limit = auth()->user()->storage_limit; // KB
                $free = max(0, $limit - $used);          // KB
                $percent = $limit > 0 ? round($used / $limit * 100, 1) : 0;
            @endphp

            {{-- User: vlastite datoteke i prostor --}}
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h2 class="h5 card-title"><i class="bi bi-files me-2"></i>My files</h2>
                        <p class="display-6 mb-3">{{ $filesCount }}</p>
                        <a href="{{ route('files.index') }}" class="btn btn-outline-primary btn-sm">View files</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h2 class="h5 card-title"><i class="bi bi-hdd me-2"></i>Storage</h2>
                        <p class="mb-2">
                            {{ number_format($free / 1024 / 1024, 2) }} / {{ number_format($limit / 1024 / 1024, 0) }} GB free space left
                        </p>
                        <div class="progress mb-3" role="progressbar" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar" style="width: {{ $percent }}%"></div>
                        </div>
                        <a href="{{ route('profile') }}" class="btn btn-outline-primary btn-sm">View profile</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
