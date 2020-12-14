<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SendMailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateController extends Controller
{
    protected $mailController;

    public function __construct(SendMailController $mailController)
    {
        $this->mailController = $mailController;
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            $remember_me  = (!empty($request->remember_me)) ? TRUE : FALSE;

            if (Auth::attempt($credentials, $remember_me)) {
                return Auth::user();
            }

            throw new \Exception('Invalid email or password');
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function logout()
    {
        try {
            Auth::guard('web')->logout();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}