@extends('layout.app')

@section('title', 'Files')

@section('content')
    @php
        $limit = auth()->user()->storage_limit; // KB
        $free = max(0, $limit - $used);          // KB
        $showForm = $errors->has('file');        // nakon greške pri uploadu odmah prikaži formu
    @endphp

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

    {{-- STATE 1: tablica datoteka --}}
    <div id="files-list" class="{{ $showForm ? 'd-none' : '' }}">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h3 mb-0">Files</h1>
                <span class="text-muted">Upload and download your files</span>
            </div>
            <button type="button" class="btn btn-primary" id="show-add">New file</button>
        </div>

        <p class="small text-muted">
            {{ number_format($free / 1024 / 1024, 2) }} / {{ number_format($limit / 1024 / 1024, 0) }} GB free space left
        </p>

        {{-- Na mobitelu (ispod md) stupci Added i Size se skrivaju, a podaci se prikazuju ispod imena --}}
        <div class="table-responsive">
        <table class="table table-hover align-middle bg-white">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col" class="d-none d-md-table-cell">Added</th>
                    <th scope="col" class="d-none d-md-table-cell">Size</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($files as $file)
                    <tr scope="row">
                        <td><i class="bi {{ $file->icon }} fs-5"></i></td>
                        <td class="text-break">
                            {{ $file->name }}
                            <div class="small text-muted d-md-none">
                                {{ $file->created_at->format('d.m.Y.') }} · {{ $file->formatted_size }}
                            </div>
                        </td>
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
                        <td colspan="5" class="text-center text-muted">No files uploaded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $files->links() }}
        </div>
    </div>

    {{-- STATE 2: dodavanje nove datoteke --}}
    <div id="files-add" class="{{ $showForm ? '' : 'd-none' }}">
        <h1 class="h3 mb-0">New file</h1>
        <span class="text-muted">Upload a new file</span>

        <form method="POST" action="{{ route('files.store') }}" enctype="multipart/form-data" class="mt-3 col-md-6 p-0">
            @csrf

            <div class="mb-3">
                <label for="file" class="form-label">File</label>
                <input type="file" id="file" name="file" required
                       class="form-control @error('file') is-invalid @enderror">
                @error('file')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Max 10 MB. Allowed: documents (pdf, doc, xls, ppt, csv, txt), images, archives (zip, rar, 7z), audio and video.</div>
            </div>

            <button type="submit" class="btn btn-success">Upload</button>
            <button type="button" class="btn btn-secondary" id="show-list">Cancel</button>
        </form>
    </div>

    @include('files.delete-modal')

    <script>
        const list = document.getElementById('files-list');
        const add = document.getElementById('files-add');

        document.getElementById('show-add').addEventListener('click', () => {
            list.classList.add('d-none');
            add.classList.remove('d-none');
        });

        document.getElementById('show-list').addEventListener('click', () => {
            add.classList.add('d-none');
            list.classList.remove('d-none');
        });
    </script>
@endsection