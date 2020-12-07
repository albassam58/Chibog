<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Exception;
use Illuminate\Support\Facades\Log;
use Socialite;


class FacebookController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToFacebook()
    {
        try {
            return Socialite::driver('facebook')->redirect();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleFacebookCallback()
    {
        try {
            $user = Socialite::driver('facebook')->fields(['first_name', 'last_name', 'email', 'id'])->usingGraphVersion('v8.0')->user();
            $create['first_name'] = $user->user['first_name'];
            $create['last_name'] = $user->user['last_name'];
            $create['email'] = $user->user['email'];
            // $create['avatar'] = $user->getAvatar();
            $create['facebook_id'] = $user->user['id'];


            $userModel = new User;
            $createdUser = $userModel->addNew($create);
            Auth::loginUsingId($createdUser->id);

            return redirect('/');
        } catch (Exception $e) {
            return $e->getMessage();
            // return redirect('auth/facebook');
        }
    }
}