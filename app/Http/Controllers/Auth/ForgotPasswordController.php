<?php

namespace App\Http\Controllers\Auth;

use DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Services\AuthenticationService;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

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

    /**
     * Forgot Password From Email
     *
     * @param string $email
     * @return void
     */
    public function forgotPasswordFromEmail(Request $request)
    {
        //You can add validation login here
        $user = DB::table('users')->where('email', '=', $request->email)->first();

        //Check if the user exists
        if (count($user) < 1) {
            return response()->json(['error' => 'ERRORRR'], 401);
        }

        //Create Password Reset Token
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => str_random(60),
            'created_at' => Carbon::now()
        ]);

        //Get the token just created above
        $tokenData = DB::table('password_resets')->where('email', $request->email)->first();

        app(AuthenticationService::class)->sendEmail('Reset Password tite', $tokenData, $request->email);

        return response()->json(['success' => true], Response::HTTP_OK);
    }
}
