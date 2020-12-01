<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable,HasFactory;

    protected $fillable = [
        'first_name',
        'second_name',
        'middle_name',
        'group',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function events()
    {
        return $this->belongsToMany(Event::class)->withPivot('role', 'code', 'presence', 'lock');
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class)->withPivot('text', 'true_false');
    }

}
