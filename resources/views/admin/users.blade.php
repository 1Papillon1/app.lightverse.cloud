@extends('layout.app')

@section('title', 'Users')

@section('content')
    <div class="mb-3">
        <h1 class="h3 mb-0">Users</h1>
        <span class="text-muted">All users list and their remaining storage</span>
    </div>

    {{-- Na mobitelu (ispod md) skrivaju se #, Name, Used i Limit; ime se prikazuje ispod usernamea --}}
    <div class="table-responsive">
    <table class="table table-hover align-middle bg-white">
        <thead>
            <tr>
                <th scope="col" class="d-none d-md-table-cell">#</th>
                <th scope="col">Username</th>
                <th scope="col" class="d-none d-md-table-cell">Name</th>
                <th scope="col">Roles</th>
                <th scope="col" class="d-none d-md-table-cell">Used</th>
                <th scope="col">Free space</th>
                <th scope="col" class="d-none d-md-table-cell">Limit</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                @php
                    $used = (int) ($user->files_sum_size ?? 0);   // KB
                    $free = max(0, $user->storage_limit - $used); // KB
                @endphp
                <tr>
                    {{-- redni broj kroz sve stranice (6, 7, 8... na drugoj stranici) --}}
                    <th scope="row" class="d-none d-md-table-cell">{{ $users->firstItem() + $loop->index }}</th>
                    <td class="text-break">
                        {{ $user->username }}
                        <div class="small text-muted d-md-none">{{ $user->first_name }} {{ $user->last_name }}</div>
                    </td>
                    <td class="d-none d-md-table-cell">{{ $user->first_name }} {{ $user->last_name }}</td>
                    <td>
                        @foreach($user->roles as $role)
                            <span class="badge {{ $role->name === \App\Models\Role::SUPER_ADMIN ? 'text-bg-dark' : 'text-bg-secondary' }}">{{ $role->name }}</span>
                        @endforeach
                    </td>
                    <td class="d-none d-md-table-cell">{{ number_format($used / 1024, 2) }} MB</td>
                    <td class="text-nowrap">{{ number_format($free / 1024, 2) }} MB</td>
                    <td class="d-none d-md-table-cell">{{ number_format($user->storage_limit / 1024 / 1024, 0) }} GB</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">No users found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $users->links() }}
    </div>
@endsection