<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $guarded = [
        'update_at',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role', 'qr_code', 'presence');
    }

}
