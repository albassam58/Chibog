<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\BaseController;
use App\Http\Controllers\SendMailController;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegisterController extends BaseController
{
	protected $mailController;

	public function __construct(SendMailController $mailController)
	{
		$this->mailController = $mailController;
	}

	public function register(Request $request)
	{
		// $request->validate([
		// 	'first_name' => 'required|string',
		// 	'last_name' => 'required|string',
		// 	'email' => 'required|email',
		// 	'password' => 'required|confirmed|min:6',
		// ]);

		try {
			DB::beginTransaction();

			$create['first_name'] = $request->first_name;
	        $create['last_name'] = $request->last_name;
	        $create['email'] = $request->email;
	        $create['mobile_number'] = $request->mobile_number;
	        $create['password'] = bcrypt($request->password);
	        $create['region'] = $request->region;
	        $create['province'] = $request->province;
	        $create['city'] = $request->city;
	        $create['barangay'] = $request->barangay;
	        $create['street'] = $request->street;
	        $create['social_media'] = $request->social_media;
	        $create['status'] = 2;

	        $vendorModel = new Vendor;
	        $vendor = $vendorModel->addNew($create);

	        $emailVerificationUrl = $vendor->createEmailVerificationUrl();

	        // send verification email
	        $details = [
	        	'subject' => 'Chibog - Email Verification',
	        	'data' => [
	        		'first_name' => $vendor->first_name,
	        		'url' => $emailVerificationUrl
	        	]
	        ];

	        $data = [
	        	'job' 		=> '\App\Jobs\SendVerificationEmail',
	        	'to'		=> $vendor->email,
	        	'cc'		=> null,
	        	'bcc'		=> null,
	        	'details'	=> $details,
	        ];

	        $this->mailController->sendMail($data);

	        DB::commit();

	        return $this->sendResponse($vendor);
	    } catch (\Exception $e) {
	    	return $this->sendError($e->getMessage());
	    } catch (\Error $e) {
	    	return $this->sendError($e->getMessage());
	    }
	}
}