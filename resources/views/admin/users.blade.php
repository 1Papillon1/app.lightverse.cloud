@extends('layout.app')

@section('title', 'Users')

@section('content')
    <div class="mb-3">
        <h1 class="h3 mb-0">Users</h1>
        <span class="text-muted">All users list and their remaining storage</span>
    </div>

    <table class="table table-hover align-middle bg-white">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Username</th>
                <th scope="col">Name</th>
                <th scope="col">Used</th>
                <th scope="col">Free space</th>
                <th scope="col">Limit</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                @php
                    $used = (int) ($user->files_sum_size ?? 0);   // KB
                    $free = max(0, $user->storage_limit - $used); // KB
                @endphp
                <tr>
                    <th scope="row">{{ $loop->iteration }}</th>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                    <td>{{ number_format($used / 1024, 2) }} MB</td>
                    <td>{{ number_format($free / 1024, 2) }} MB</td>
                    <td>{{ number_format($user->storage_limit / 1024 / 1024, 0) }} GB</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">No users found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $users->links() }}
    </div>
@endsection