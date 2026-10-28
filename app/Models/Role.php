<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    // Nazivi uloga na jednom mjestu, umjesto stringova razbacanih po kodu
    public const USER = 'user';

    public const SUPER_ADMIN = 'super_admin';

    protected $table = 'roles';

    protected $fillable = [
        'name',
    ];
}
