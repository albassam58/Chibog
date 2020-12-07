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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'facebook_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
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
        if (array_key_exists('facebook_id', $input)) {
            $check = static::where('facebook_id', $input['facebook_id'])->first();
        }

        if(is_null($check)){
            return static::create($input);
        }

        return $check;
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
