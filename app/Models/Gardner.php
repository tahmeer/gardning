<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class Gardner extends Model implements AuthenticatableContract
{
    use Authenticatable,HasApiTokens,Notifiable;
    protected $guard = 'gardner';
}
