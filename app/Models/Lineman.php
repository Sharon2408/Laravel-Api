<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Lineman extends Authenticatable implements JWTSubject 
{
    use  HasApiTokens,HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_no',
        'lineman_id',
    ];

    protected $hidden = [
        'password',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'role_id' => $this->role_id,
            'consumer_id' => $this->consumer_id
        ];
    }

    public function lineman(){
        return $this->hasMany(Task::class);
    }
}