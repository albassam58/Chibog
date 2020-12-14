<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\BaseController;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthenticateController extends BaseController
{
	public function login(Request $request)
	{
		$request->validate([
			'email' => 'required|email',
			'password' => 'required|string',
			'device_name' => 'required'
		]);

		try {
			$vendor = Vendor::where('email', $request->email)->firstOrFail();

			if (!Hash::check($request->password, $vendor->password)) {
				throw new \Exception('Invalid email or password');
			}

			// if (isset($request->device_name)) {
			$device = $request->device_name;
			// } else {
			// 	$device = Browser::platformName();
			// 	$browserName = Browser::browserName();
			// }

			$tokenResult = $vendor->createToken($device)->plainTextToken;

			return $this->sendResponse($tokenResult);
		} catch(\Exception $e) {
			return $this->sendError($e->getMessage());
		}
	}

	public function logout(Request $request)
	{
		try {
			// Revoke the user's current token...
			$request->user()->currentAccessToken()->delete();
			return $this->sendResponse();
		} catch (\Exception $e) {
			return $this->sendError($e->getMessage());
		}
	}

	public function logoutDevice(Request $request, $id)
	{
		try {
			// Revoke the user's current token...
			$vendor = $request->user();
			$vendor->tokens()->where('id', $id)->delete();
			return $this->sendResponse();
		} catch (\Exception $e) {
			return $this->sendError($e->getMessage());
		}
	}

	public function logoutAll(Request $request)
	{
		try {
			// Revoke the user's current token...
			// $vendor = $request->user();
			// $vendor->tokens()->where('id', $id)->delete();
			// current id
			$vendor = $request->user();
			$id = $vendor->currentAccessToken()->id;
			$vendor->tokens()->where('id', '<>', $id)->delete();

			return $this->sendResponse();
		} catch (\Exception $e) {
			return $this->sendError($e->getMessage());
		}
	}

	public function forgotPassword(Request $request)
    {
    	$request->validate(['email' => 'required|email']);

    	try {

	        $status = Password::sendResetLink(
	            $request->only('email')
	        );

	        if ($status !== Password::RESET_LINK_SENT) {
	        	throw new \Exception(__($status));
	        }

	        $response = ['status' => __($status)];

	        return $this->sendResponse($response);
	    } catch (\Exception $e) {
	    	return $this->sendError($e->getMessage());
	    }
    }

    public function resetPassword(Request $request)
    {
    	$request->validate([
	        'token' => 'required',
	        'email' => 'required|email',
	        'password' => 'required|min:8|confirmed',
	    ]);

	    try {
		    $status = Password::reset(
		        $request->only('email', 'password', 'password_confirmation', 'token'),
		        function ($user, $password) use ($request) {
		            $user->forceFill([
		                'password' => Hash::make($password)
		            ])->save();

		            $user->setRememberToken(Str::random(60));

		            event(new PasswordReset($user));
		        }
		    );

		    if ($status != Password::PASSWORD_RESET) {
		    	throw new \Exception(__($status));
		    }

		    // return $status == Password::PASSWORD_RESET
		    //             ? redirect('/')
		    //             : back()->withErrors(['email' => [__($status)]]);
		    return $this->sendResponse();
		} catch (\Exception $e) {
			return $this->sendError($e->getMessage());
		}
    }
}