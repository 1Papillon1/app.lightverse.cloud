<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $files = $user->files()->latest()->paginate(5);
        if ($files->isEmpty() && $files->currentPage() > 1) {
            return redirect()->route('files', ['page' => $files->lastPage()]);
        }
        $used = (int) $user->files()->sum('size'); // u KB

        return view('files.index', compact('files', 'used'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // max 10 MB (vrijednost je u KB)
        ]);

        $user = auth()->user();
        $uploaded = $request->file('file');

        $sizeKb = (int) ceil($uploaded->getSize() / 1024);
        $used = (int) $user->files()->sum('size');

        if ($used + $sizeKb > $user->storage_limit) {
            return back()->withErrors(['file' => 'Not enough free storage space.']);
        }

        $user->files()->create([
            'name' => $uploaded->getClientOriginalName(),
            'storage_path' => $uploaded->store('users/' . $user->id), // spremanje svih datoteka usera u njegovu podmapu
            'extension'     => strtolower($uploaded->getClientOriginalExtension()),
            'size'          => $sizeKb,
        ]);

        return back()->with('success', 'File uploaded successfully.');
    }

    public function download(File $file)
    {
        Gate::authorize('download', $file);

        return Storage::download($file->storage_path, $file->name);
    }

    public function destroy(File $file)
    {
        Gate::authorize('delete', $file);

        Storage::delete($file->storage_path);
        $file->delete();

        return back()->with('success', 'File deleted successfully.');
    }
}