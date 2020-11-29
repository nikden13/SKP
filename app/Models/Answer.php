<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'true_false',
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
        'true_false',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

}
