<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ResetPassword;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\ResetPasswordRequest;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function sendMail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ],[
            'email.required' => 'Email không được bỏ trống.',
            'email.exists' => 'Email không tồn tại.',
        ]);
        
        $user = User::where('email','=',($request->email))->first();
        if(!$user){
            return response()->json([
                'message' => 'Email not found'
            ]);
        }
        // dd($user);
        $passwordReset = ResetPassword::updateOrCreate([
            'email' => $user->email,
        ], [
            'token' => Str::random(6),
        ]);
        if ($passwordReset) {
            $user->notify(new ResetPasswordRequest($passwordReset->token));
        }
  
        return response()->json([
        'message' => 'We have sent your registration OTP code!'
        ]);
    }
}
