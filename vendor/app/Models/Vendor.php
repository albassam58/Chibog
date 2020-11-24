<?php

namespace App\Models;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Vendor extends BaseModel
{
    use HasFactory;

    /*
        Status:
            1. Pending
            2. Approved
            3. Rejected
    */

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
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
     * Generate temporary signed url for email verification
     */
    public function createEmailVerificationUrl()
    {
        $temporarySignedUrl = URL::temporarySignedRoute(
            'vendor.verify', Carbon::now()->addMinutes(60), ['id' => $this->id]
        );

        $url = config('app.url') . "/email/verification?email=" . $this->email . "&queryUrl=" . urlencode($temporarySignedUrl);

        return $url;
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
