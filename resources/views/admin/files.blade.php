@extends('layout.app')

@section('title', 'All files')

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mb-3">
        <h1 class="h3 mb-0">All files</h1>
        <span class="text-muted">Files uploaded by all users</span>
    </div>

    {{-- Na mobitelu (ispod md) stupci Added By, Added i Size se skrivaju, a podaci se prikazuju ispod imena --}}
    <div class="table-responsive">
    <table class="table table-hover align-middle bg-white">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col" class="d-none d-md-table-cell">Added By</th>
                <th scope="col" class="d-none d-md-table-cell">Added</th>
                <th scope="col" class="d-none d-md-table-cell">Size</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($files as $file)
                <tr>
                    <td><i class="bi {{ $file->icon }} fs-5"></i></td>
                    <td class="text-break">
                        {{ $file->name }}
                        <div class="small text-muted d-md-none">
                            Added by {{ $file->user->first_name }} {{ $file->user->last_name }} · {{ $file->formatted_size }}
                        </div>
                    </td>
                    <td class="d-none d-md-table-cell">{{ $file->user->first_name }} {{ $file->user->last_name }}</td>
                    <td class="d-none d-md-table-cell">{{ $file->created_at->format('d.m.Y. H:i') }}</td>
                    <td class="d-none d-md-table-cell">{{ $file->formatted_size }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('files.download', $file) }}" class="btn btn-sm btn-outline-primary"
                           title="Download" aria-label="Download {{ $file->name }}">
                            <i class="bi bi-download"></i><span class="d-none d-md-inline ms-1">Download</span>
                        </a>

                        <button type="button" class="btn btn-sm btn-outline-danger"
                                title="Delete" aria-label="Delete {{ $file->name }}"
                                data-bs-toggle="modal" data-bs-target="#delete-modal"
                                data-action="{{ route('files.destroy', $file) }}" data-name="{{ $file->name }}">
                            <i class="bi bi-trash"></i><span class="d-none d-md-inline ms-1">Delete</span>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">No files uploaded yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $files->links() }}
    </div>

    @include('files.delete-modal')
@endsection