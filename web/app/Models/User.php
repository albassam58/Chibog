<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $appends = ['full_name', 'reversed_full_name'];

    public function getFullNameAttribute() {
        return $this->first_name . " " . $this->last_name;
    }

    public function getReversedFullNameAttribute() {
        return $this->last_name . ", " . $this->first_name;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'provider_name', 'provider_id', 'api_token', 'region', 'province', 'city', 'barangay', 'street', 'gender', 'birthday',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'provider_id', 'provider_name', 'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * check if facebook id already exists
     * it will return object and if not exists then create new user and return user object.
     */
    public function addNew($input)
    {
        $check = null;
        if (array_key_exists('provider_id', $input)) {
            $check = static::where('provider_id', $input['provider_id'])->where('provider_name', $input['provider_name'])->first();
        }

        if(is_null($check)){
            return static::create($input);
        }

        return $check;
    }
}
