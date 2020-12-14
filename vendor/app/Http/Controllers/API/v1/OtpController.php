<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\Vendor;
use Illuminate\Http\Request;

class OtpController extends BaseController
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
     * @param  \App\Models\Otp  $otp
     * @return \Illuminate\Http\Response
     */
    public function show(Otp $otp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Otp  $otp
     * @return \Illuminate\Http\Response
     */
    public function edit(Otp $otp)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Otp  $otp
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Otp $otp)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Otp  $otp
     * @return \Illuminate\Http\Response
     */
    public function destroy(Otp $otp)
    {
        //
    }

    /**
     * Send One-time Password to mobile number
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function send(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|string'
        ]);

        try {
            $vendor = Vendor::where('id', auth('sanctum')->user()->id)->where('mobile_number', $request->mobile_number)->first();

            if (!$vendor) {
                throw new \Exception('Vendor not found.');
            }

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

            $result = sendSMS($params);

            if ($result != 0) {
                throw new \Exception("Error Num $result was encountered!");
            }

            return $this->sendResponse($vendor);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Verify One-time Password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|string',
            'otp'           => 'required|digits:6'
        ]);

        try {
            $otp = Otp::where('mobile_number', $request->mobile_number)->where('otp', $request->otp)->first();

            // OTP not found
            if (!$otp) {
                throw new \Exception('Invalid OTP.');
            }

            // check if OTP is already expired
            $dbtimestamp = strtotime($otp->created_at);
            if (time() - $dbtimestamp > 5 * 60) {
                // 5 mins has passed
                throw new \Exception('Expired OTP.', 498);
            }

            // verify vendor
            $vendor = Vendor::where('id', auth('sanctum')->user()->id)->where('mobile_number', $otp->mobile_number)->first();

            if (!$vendor) {
                throw new \Exception('Vendor not found.');
            }

            $vendor->update([
                'mobile_verified_at' => date('Y-m-d H:i:s')
            ]);

            // delete all generated OTPs for mobile number
            Otp::where('mobile_number', $request->mobile_number)->delete();

            return $this->sendResponse($otp);
        } catch (\Exception $e) {
            $code = 500;
            if ($e->getCode()) $code = $e->getCode();
            return $this->sendError($e->getMessage(), null, $code);
        }
    }
}
