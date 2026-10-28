<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFileRequest;
use App\Models\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $files = $user->files()->latest()->paginate(5);
        if ($files->isEmpty() && $files->currentPage() > 1) {
            return redirect()->route('files.index', ['page' => $files->lastPage()]);
        }
        $used = (int) $user->files()->sum('size'); // u KB

        return view('files.index', compact('files', 'used'));
    }

    public function store(StoreFileRequest $request)
    {
        $user = auth()->user();
        $uploaded = $request->file('file');

        $sizeKb = (int) ceil($uploaded->getSize() / 1024);
        $used = (int) $user->files()->sum('size');

        if ($used + $sizeKb > $user->storage_limit) {
            return back()->withErrors(['file' => 'Not enough free storage space.']);
        }

        // spremanje svih datoteka usera u njegovu podmapu
        $path = $uploaded->store('users/'.$user->id);

        if (! $path) {
            Log::error('File upload failed: could not write to storage', ['user_id' => $user->id]);

            return back()->withErrors(['file' => 'Upload failed. Please try again.']);
        }

        try {
            $file = $user->files()->create([
                'name' => $uploaded->getClientOriginalName(),
                'storage_path' => $path,
                'extension' => strtolower($uploaded->getClientOriginalExtension()),
                'size' => $sizeKb,
            ]);
        } catch (\Throwable $e) {
            // zapis u bazu nije uspio, pa brišemo datoteku da ne ostane na disku bez zapisa
            Storage::delete($path);
            Log::error('File upload failed: could not save to database', ['user_id' => $user->id, 'error' => $e->getMessage()]);

            return back()->withErrors(['file' => 'Upload failed. Please try again.']);
        }

        Log::info('File uploaded', ['file_id' => $file->id, 'user_id' => $user->id]);

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

        // prvo brišemo zapis iz baze: ako to ne uspije, datoteka ostaje netaknuta
        try {
            $file->delete();
        } catch (\Throwable $e) {
            Log::error('File delete failed', ['file_id' => $file->id, 'error' => $e->getMessage()]);

            return back()->with('error', 'File could not be deleted. Please try again.');
        }

        // zapis je obrisan; ako datoteka ne postoji na disku, samo zabilježimo upozorenje
        if (! Storage::delete($file->storage_path)) {
            Log::warning('File deleted from database but not found in storage', ['file_id' => $file->id, 'path' => $file->storage_path]);
        }

        Log::info('File deleted', ['file_id' => $file->id, 'owner_id' => $file->user_id, 'deleted_by' => auth()->id()]);

        return back()->with('success', 'File deleted successfully.');
    }
}
