<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\BaseController;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginController extends BaseController
{
	public function authenticate(Request $request)
	{
		$request->validate([
			'email' => 'required|email',
			'password' => 'required|string'
		]);

		try {
			$vendor = Vendor::where('email', $request->email)->firstOrFail();

			if (Hash::check($request->password, $vendor->password)) {
				$vendor->rollApiKey(); // Model Function
				return $this->sendResponse($vendor);
			}

			throw new ModelNotFoundException('Invalid email or password');
		} catch (ModelNotFoundException $error) {
			return $this->sendError("Invalid email or password", [], 401);
		} catch(\Exception $error) {
			return $this->sendError($error->getMessage());
		}
	}

	public function logout(Request $request)
	{
		// return $request->user();
		$request->user()->removeApiKey();
		// User::find(Auth::user())
	}
}