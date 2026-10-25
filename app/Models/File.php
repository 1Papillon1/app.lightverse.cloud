<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'storage_path',
        'extension',
        'size',
    ];
}


// github push description of changes:
// Added models for User, Role, and File