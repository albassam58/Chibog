<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\BaseController;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Browser;

class LoginController extends BaseController
{
	public function login(Request $request)
	{
		$request->validate([
			'email' => 'required|email',
			'password' => 'required|string'
		]);

		try {
			$user = User::where('email', $request->email)->firstOrFail();

			if (!Hash::check($request->password, $user->password)) {
				throw new \Exception('Invalid email or password');
			}

			if (isset($request->device_name)) {
				$device = $request->device_name;
			} else {
				$device = Browser::platformName();
			}

			$tokenResult = $user->createToken($device ? $device : "Unknown")->plainTextToken;
			
			return $this->sendResponse($tokenResult);
		} catch (ModelNotFoundException $e) {
			return $this->sendError("Invalid email or password", [], 401);
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
}