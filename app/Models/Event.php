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

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role', 'code', 'presence', 'lock');
    }

    public function test()
    {
        return $this->hasOne(Test::class);
    }

    public function code()
    {
        return $this->hasOne(Code::class);
    }

}
