<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class RegisterController extends BaseController
{
	public function register(Request $request)
	{
		$request->validate([
			'first_name' => 'required|string',
			'last_name' => 'required|string',
			'email' => 'required|email|unique:users',
			'password' => 'required|confirmed|min:6',
		]);

		try {
			$create['first_name'] = $request->first_name;
	        $create['last_name'] = $request->last_name;
	        $create['email'] = $request->email;
	        $create['password'] = bcrypt($request->password);

	        $userModel = new User;
	        $createdUser = $userModel->addNew($create);
	    } catch (\Exception $error) {
	    	return $this->sendError($error->getMessage());
	    }
	}
}