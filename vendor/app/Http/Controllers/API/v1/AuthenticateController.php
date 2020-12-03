<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\BaseController;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
}