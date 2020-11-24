<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
        'first_name', 'last_name', 'email', 'password', 'provider_name', 'provider_id', 'api_token', 'region', 'province', 'city', 'barangay', 'street',
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
            $api_token = null;
            do {
                $api_token = Str::random(60);
            } while (static::where('api_token', $api_token)->exists());

            $input['api_token'] = $api_token;
        }

        if(is_null($check)){
            return static::create($input);
        } else {
            $user = static::find($check->id);
            $user->update([
                'api_token' => $api_token
            ]);
        }

        return $user;
    }

    /**
     * Roll API Key
     */
    public function rollApiKey()
    {
       do {
          $this->api_token = Str::random(60);
       } while ($this->where('api_token', $this->api_token)->exists());

       $this->save();
    }

    /**
     * Remove API Key
     */
    public function removeApiKey()
    {
        $this->api_token = null;
        $this->save();
    }
}
