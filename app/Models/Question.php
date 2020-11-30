<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'test_id',
    ];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('text', 'true_false');
    }

}
