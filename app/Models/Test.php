<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'time_limit',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role', 'presence');
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

}
