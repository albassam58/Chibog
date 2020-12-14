<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\BaseController;
use App\Http\Controllers\SendMailController;
use App\Jobs\SendOtp;
use App\Models\Otp;
use App\Models\Vendor;
use App\Notifications\EmailVerification;
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
		$request->validate([
			'first_name' 	=> 'required|string',
			'last_name' 	=> 'required|string',
			'mobile_number' => 'required|string',
			'email' 		=> 'required|email|unique:vendors,email',
			'region'        => 'required|string',
            'province'      => 'required|string',
            'city'          => 'required|string',
            'barangay'      => 'required|string',
            'street'        => 'required|string',
            'password'      => 'required|min:8|confirmed'
		]);

		try {
			DB::beginTransaction();

			$request->mobile_number = str_replace("+63", "0", $request->mobile_number);

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
	        $create['status'] = 2;

	        $vendorModel = new Vendor;
	        $vendor = $vendorModel->addNew($create);

	        $credentials = $request->only('email', 'password');

	        Auth::loginUsingId($vendor->id);

            $otp = generateNumericOTP();

            // save otp
            Otp::create([
                'mobile_number' => $request->mobile_number,
                'otp'           => $otp
            ]);

            // send OTP
            $params = array(
                '1' => $request->mobile_number,
                '2' => "Your OTP is: $otp",
                '3' => "TR-ALBAS828316_X26FZ",
                'passwd' => 'tc$pe]!bxw'
            );

            SendOtp::dispatch($params)->onQueue('sms');

            DB::commit();

	        return $this->sendResponse($vendor);
	    } catch (\Exception $e) {
	    	DB::rollBack();
	    	return $this->sendError($e->getMessage());
	    } catch (\Error $e) {
	    	DB::rollBack();
	    	return $this->sendError($e->getMessage());
	    }
	}
}