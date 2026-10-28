<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFileRequest extends FormRequest
{
    // Svaki prijavljeni korisnik smije uploadati (ruta je već iza auth middlewarea)
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // max 10 MB (vrijednost je u KB)
            // dopušteni tipovi su isti kao oni za koje postoji ikona u File modelu; ostalo (npr. .exe, .php) se odbija
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,csv,ppt,pptx,jpg,jpeg,png,gif,svg,webp,zip,rar,7z,txt,mp3,wav,mp4,mov,avi',
        ];
    }
}
