<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Cloud')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">

{{-- navbar-expand-md: na mobitelu (ispod 768px) linkovi se skupljaju u hamburger izbornik --}}
<nav class="navbar navbar-expand-md navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">Cloud</a>

        @auth
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav"
                    aria-controls="main-nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="main-nav">
            <ul class="navbar-nav me-auto">
                {{-- active klasa označava trenutnu stranicu u navigaciji --}}
                @if(auth()->user()->hasRole(\App\Models\Role::USER))
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('files.index') ? 'active' : '' }}" href="{{ route('files.index') }}">Files</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}" href="{{ route('profile') }}">Profile</a></li>
                @endif
                @if(auth()->user()->hasRole(\App\Models\Role::SUPER_ADMIN))
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.files') ? 'active' : '' }}" href="{{ route('admin.files') }}">All Files</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}" href="{{ route('admin.users') }}">Users</a></li>
                @endif
            </ul>

            <div class="d-flex align-items-center gap-2 py-2 py-md-0">
                <span class="navbar-text">{{ auth()->user()->username }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            </div>
            </div>
        @endauth

    </div>
</nav>

<main class="container">
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>