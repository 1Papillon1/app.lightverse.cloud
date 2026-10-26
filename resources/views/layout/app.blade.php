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

<nav class="navbar navbar-expand navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="/">Cloud</a>

        @auth
            <ul class="navbar-nav me-auto">


                @if(auth()->user()->hasRole('user'))
                    <li class="nav-item"><a class="nav-link" href="/files">Files</a></li>
                    <li class="nav-item"><a class="nav-link" href="/profile">Profile</a></li>
                @endif
                @if(auth()->user()->hasRole('super_admin'))
                    <li class="nav-item"><a class="nav-link" href="/admin/files">All Files</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/users">Users</a></li>
                @endif
            </ul>

             <span class="navbar-text" style="margin-right:0.5rem">
                {{auth()->user()->username}}
            </span>
            <form method="POST" action="/logout">
                @csrf
                <button class="btn btn-outline-light btn-sm">Logout</button>
            </form>
        @endauth

    </div>
</nav>

<main class="container">
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>