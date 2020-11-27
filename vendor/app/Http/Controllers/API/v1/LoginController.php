<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\BaseController;
use App\Models\Vendor;
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
			$vendor = Vendor::where('email', $request->email)->firstOrFail();

			if (!Hash::check($request->password, $vendor->password)) {
				throw new \Exception('Invalid email or password');
			}

			if (isset($request->device_name)) {
				$device = $request->device_name;
			} else {
				$device = Browser::platformName();
			}

			$tokenResult = $vendor->createToken($device ? $device : "Unknown")->plainTextToken;

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
}