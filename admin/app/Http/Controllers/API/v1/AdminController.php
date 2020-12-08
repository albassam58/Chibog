<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\BaseController;
use App\Http\Controllers\SendMailController;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends BaseController
{
    protected $mailController;

    public function __construct(SendMailController $mailController)
    {
        $this->mailController = $mailController;
    }

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
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function show(Admin $admin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function edit(Admin $admin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Admin $admin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function destroy(Admin $admin)
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
    public function resend($id, $email)
    {
        try {
            $vendor = Vendor::where('id', $id)->where('email', $email)->first();

            if (!$vendor) {
                throw new \Exception("Vendor not found");
            }

            if ($vendor->email_verified_at) {
                return $this->sendError("Email is already verified!", [], 422);
            }

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
                'job'       => '\App\Jobs\SendVerificationEmail',
                'to'        => $vendor->email,
                'cc'        => null,
                'bcc'       => null,
                'details'   => $details,
            ];

            $this->mailController->sendMail($data);

            return $this->sendResponse("Email verification is successfully resent.");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
