<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Exception;
use Illuminate\Support\Facades\Log;
use Socialite;
use Browser;

class SocialiteController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function socialiteRedirect($provider)
    {
        try {
            $url = null;
            switch ($provider) {
                case 'facebook':
                    $url = Socialite::driver($provider)->usingGraphVersion('v8.0')->redirect()->getTargetUrl();
                    break;
                case 'google':
                    $url = Socialite::driver($provider)->redirect()->getTargetUrl();
                    break;
                
                default:
                    # code...
                    break;
            }
            return $url;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleSocialiteCallback($provider)
    {
        try {
            $user = null;
            switch ($provider) {
                case 'facebook':
                    $user = Socialite::driver('facebook')->fields(['first_name', 'last_name', 'email', 'id'])->usingGraphVersion('v8.0')->user();
                    $create['first_name'] = $user->user['first_name'];
                    $create['last_name'] = $user->user['last_name'];
                    $create['email'] = $user->user['email'];
                    $create['provider_name'] = $provider;
                    $create['provider_id'] = $user->user['id'];
                    break;
                case 'google':
                    $user = Socialite::driver('google')->user();
                    $create['first_name'] = $user->user['given_name'];
                    $create['last_name'] = $user->user['family_name'];
                    $create['email'] = $user->user['email'];
                    $create['provider_name'] = $provider;
                    $create['provider_id'] = $user->user['id'];
                    break;
                
                default:
                    # code...
                    break;
            }

            $userModel = new User;
            $createdUser = $userModel->addNew($create);
            // Auth::loginUsingId($createdUser->id);

            if (isset($request->device_name)) {
                $device = $request->device_name;
            } else {
                $device = Browser::platformName();
            }

            $tokenResult = $createdUser->createToken($device ? $device : "Unknown")->plainTextToken;
            
            // Create new view (I use callback.blade.php), and send the token and the name.
            return view('socialite-callback', [
                'current_user' => $createdUser,
                'api_token' => $tokenResult,
                'error' => null
            ]);
        } catch (Exception $e) {
            return view('socialite-callback', [
                'current_user' => [],
                'api_token' => null,
                'error' => $e->getMessage()
            ]);
        }
    }
}