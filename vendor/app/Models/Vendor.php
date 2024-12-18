<?php

namespace App\Models;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class Vendor extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /*
        Status:
            1. Pending
            2. Approved
            3. Rejected
    */

    protected $guarded = [];

    protected $appends = ['full_name', 'reversed_full_name'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    public function getFullNameAttribute() {
        return $this->first_name . " " . $this->last_name;
    }

    public function getReversedFullNameAttribute() {
        return $this->last_name . ", " . $this->first_name;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

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
     * Generate temporary signed url for email verification
     */
    public function createEmailVerificationUrl()
    {
        $temporarySignedUrl = URL::temporarySignedRoute(
            'vendor.verify', Carbon::now()->addMinutes(60), ['id' => $this->id]
        );

        $url = config('app.url') . "/verification?email=" . $this->email . "&queryUrl=" . urlencode($temporarySignedUrl);

        return $url;
    }
}
