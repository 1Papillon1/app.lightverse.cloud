@extends('layout.app')

@section('title', 'All files')

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mb-3">
        <h1 class="h3 mb-0">All files</h1>
        <span class="text-muted">Files uploaded by all users</span>
    </div>

    <table class="table table-hover align-middle bg-white">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Added By</th>
                <th scope="col">Added</th>
                <th scope="col">Size</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($files as $file)
                <tr>
                    <td><i class="bi {{ $file->icon }} fs-5 me-2"></i></td>
                    <td>{{ $file->original_name }}</td>
                    <td>{{ $file->user->first_name }} {{ $file->user->last_name }}</td>
                    <td>{{ $file->created_at->format('d.m.Y. H:i') }}</td>
                    <td>{{ $file->formatted_size }}</td>
                    <td class="text-end">
                        <a href="{{ url('files/' . $file->id . '/download') }}"
                           class="btn btn-sm btn-outline-primary">Download</a>

                        <form method="POST" action="{{ url('files/' . $file->id) }}" class="d-inline"
                              onsubmit="return confirm('Delete this file?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">No files uploaded yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $files->links() }}
    </div>
@endsection