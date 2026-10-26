@extends('layout.app')

@section('title', 'Profile')

@section('content')
    @php
        $showForm = $errors->any();                              // nakon greške validacije odmah prikaži formu
        $limit = $user->storage_limit;                           // KB
        $free = max(0, $limit - $user->getStorageUsed());        // KB
    @endphp

    @if(session('success'))
         <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- STATE 1: pregled profila --}}
    <div id="profile-view" class="{{ $showForm ? 'd-none' : '' }}">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Profile</h1>
            <button type="button" class="btn btn-primary" id="show-edit">Edit</button>
        </div>

        <dl class="row bg-white p-3 rounded border">
            <dt class="col-sm-3">Username</dt>
            <dd class="col-sm-9">{{ $user->username }}</dd>

            <dt class="col-sm-3">First name</dt>
            <dd class="col-sm-9">{{ $user->first_name }}</dd>

            <dt class="col-sm-3">Last name</dt>
            <dd class="col-sm-9">{{ $user->last_name }}</dd>

            <dt class="col-sm-3">Address</dt>
            <dd class="col-sm-9">{{ $user->address ?: '-' }}</dd>

            <dt class="col-sm-3">Roles</dt>
            <dd class="col-sm-9">
                @foreach($roles as $role)
                    <span class="badge text-bg-secondary">{{ $role }}</span>
                @endforeach
            </dd>

            <dt class="col-sm-3">Storage</dt>
            <dd class="col-sm-9 mb-0">
                {{ number_format($free / 1024 / 1024, 2) }} / {{ number_format($limit / 1024 / 1024, 0) }} GB free space left
            </dd>
        </dl>
    </div>

    {{-- STATE 2: uređivanje profila --}}
    <div id="profile-edit" class="{{ $showForm ? '' : 'd-none' }}">
        <h1 class="h3 mb-3">Edit Profile</h1>

        <form method="POST" action="{{ route('profile.update') }}" class="col-md-6 p-0">
            @csrf

            <div class="mb-3">
                <label for="first_name" class="form-label">First name</label>
                <input type="text" id="first_name" name="first_name" required
                       class="form-control @error('first_name') is-invalid @enderror"
                       value="{{ old('first_name', $user->first_name) }}">
                @error('first_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="last_name" class="form-label">Last name</label>
                <input type="text" id="last_name" name="last_name" required
                       class="form-control @error('last_name') is-invalid @enderror"
                       value="{{ old('last_name', $user->last_name) }}">
                @error('last_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" id="address" name="address"
                       class="form-control @error('address') is-invalid @enderror"
                       value="{{ old('address', $user->address) }}">
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-success">Save</button>
            <button type="button" class="btn btn-secondary" id="cancel-edit">Cancel</button>
        </form>
    </div>

    <script>
        const profileView = document.getElementById('profile-view');
        const profileEdit = document.getElementById('profile-edit');

        document.getElementById('show-edit').addEventListener('click', () => {
            profileView.classList.add('d-none');
            profileEdit.classList.remove('d-none');
        });

        document.getElementById('cancel-edit').addEventListener('click', () => {
            profileEdit.classList.add('d-none');
            profileView.classList.remove('d-none');
        });
    </script>
@endsection