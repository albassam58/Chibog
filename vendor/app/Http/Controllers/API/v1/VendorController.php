<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\BaseController;
use App\Jobs\SendOtp;
use App\Models\Otp;
use App\Models\Vendor;
use App\Notifications\EmailVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function tokens(Request $request)
    {
        try {
            $vendor = $request->user();

            $id = $vendor->currentAccessToken()->id;
            $tokens = $vendor->tokens;

            $filtered = $tokens->filter(function ($object) use($id) {
                return $object->id !== $id;
            });

            return $this->sendResponse($filtered);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function show(Vendor $vendor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function edit(Vendor $vendor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id = auth('sanctum')->user()->id;

        $request->validate([
            'first_name'    => 'sometimes|required|string',
            'last_name'     => 'sometimes|required|string',
            'mobile_number' => 'sometimes|required|string',
            'email'         => "sometimes|required|email|unique:vendors,email,$id",
            'region'        => 'sometimes|required|string',
            'province'      => 'sometimes|required|string',
            'city'          => 'sometimes|required|string',
            'barangay'      => 'sometimes|required|string',
            'street'        => 'sometimes|required|string',
            'password'      => 'sometimes|required|min:8|confirmed'
        ]);

        try {
            DB::beginTransaction();

            $vendor = Vendor::find($id);

            if ($request->has('password')) {
                $vendor->update([
                    'password' => bcrypt($request->password)
                ]);
            }

            if ($request->has('mobile_number')) {
                $request->merge([
                    'mobile_number' => str_replace("+63", "0", $request->mobile_number)
                ]);
            }

            $vendor->update($request->except(['password', 'password_confirmation']));

            // remove email_verified_at
            // send a verification email
            if ($request->has('email')) {
                $vendor->update([
                    'email_verified_at' => null
                ]);

                $vendor->notify(new EmailVerification);
            }

            // remove mobile_verified_at
            // send an OTP
            if ($request->has('mobile_number')) {
                $vendor->update([
                    'mobile_verified_at' => null
                ]);

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
            }

            DB::commit();

            return $this->sendResponse($vendor);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vendor $vendor)
    {
        //
    }

    /**
     * Display the current logged in user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function currentUser(Request $request)
    {
        try {
            return $this->sendResponse($request->user());
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Verify the vendor.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request, $id)
    {
        try {
            $vendor = Vendor::findOrFail($id);

            if ($vendor->email_verified_at) {
                return $this->sendError("Email is already verified!", [], 422);
            }

            $date = date('Y-m-d H:i:s');
            $vendor->email_verified_at = $date;
            $vendor->save();

            return $this->sendResponse("Email Verified!");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        } catch (\Error $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Resend email verification.
     *
     * @param  integer  $id
     * @param  string   $email
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'email' => 'required|email',
        ]);

        try {
            DB::beginTransaction();

            $id = $request->id;
            $email = $request->email;

            $vendor = Vendor::where('id', $id)->where('email', $email)->first();

            if (!$vendor) {
                throw new \Exception("Vendor not found");
            }

            if ($vendor->email_verified_at) {
                return $this->sendError("Email is already verified!", [], 422);
            }

            $vendor->notify(new EmailVerification);

            DB::commit();

            return $this->sendResponse("Email verification is successfully resent.");
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }
}
