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
			'first_name' 	=> 'required|string',
			'last_name' 	=> 'required|string',
			'email' 		=> 'required|email|unique:users',
			'gender' 		=> 'required|integer',
			'birthday' 		=> 'required|date_format:Y-m-d|before:today',
			'password' 		=> 'required|confirmed|min:8',
			'region' 		=> 'required|string',
    		'province' 		=> 'required|string',
		    'city' 			=> 'required|string',
		    'barangay' 		=> 'required|string',
		    'street' 		=> 'required|string'
		]);

		try {
			$create['first_name'] = $request->first_name;
	        $create['last_name'] = $request->last_name;
	        $create['email'] = $request->email;
	        $create['gender'] = $request->gender;
	        $create['birthday'] = $request->birthday;
	        $create['password'] = bcrypt($request->password);
	        $create['region'] = $request->region;
	        $create['province'] = $request->province;
	        $create['city'] = $request->city;
	        $create['barangay'] = $request->barangay;
	        $create['street'] = $request->street;

	        $userModel = new User;
	        $createdUser = $userModel->addNew($create);

	        return $this->sendResponse($createdUser);
	    } catch (\Exception $error) {
	    	return $this->sendError($error->getMessage());
	    }
	}
}