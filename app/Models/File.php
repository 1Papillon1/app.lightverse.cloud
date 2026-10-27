<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use App\Models\User;

class File extends Model
{
    use HasFactory, Notifiable;


    protected $table = 'files';

    protected $fillable = [
        'user_id',
        'name',
        'storage_path',
        'extension',
        'size',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

     // Prilagodba ikone prema ekstenziji
    public function getIconAttribute(): string
    {
        return match ($this->extension) {
            'pdf'                                     => 'bi-file-earmark-pdf',
            'doc', 'docx'                             => 'bi-file-earmark-word',
            'xls', 'xlsx', 'csv'                      => 'bi-file-earmark-excel',
            'ppt', 'pptx'                             => 'bi-file-earmark-ppt',
            'jpg', 'jpeg', 'png', 'gif', 'svg', 'webp' => 'bi-file-earmark-image',
            'zip', 'rar', '7z'                        => 'bi-file-earmark-zip',
            'txt'                                     => 'bi-file-earmark-text',
            'mp3', 'wav'                              => 'bi-file-earmark-music',
            'mp4', 'mov', 'avi'                       => 'bi-file-earmark-play',
            default                                   => 'bi-file-earmark',
        };
    }
 
    // Format veličine datoteke, za prikaz u tablici u MB
    public function getFormattedSizeAttribute(): string
    {
        return $this->size >= 1024
            ? number_format($this->size / 1024, 2) . ' MB'
            : $this->size . ' KB';
    }

    
}


