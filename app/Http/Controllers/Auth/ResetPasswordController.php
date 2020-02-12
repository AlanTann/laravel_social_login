<?php

namespace App\Http\Controllers\Auth;

use DB;
use Auth;
use App\User;
use App\Http\Controllers\Controller;
use App\Services\AuthenticationService;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Reset the given user's password.
     *
     * @param  Request $request
     */
    protected function resetPassword(Request $request)
    {
        $user = Auth::user();
        $user->password = bcrypt($request->password);
        $user->save();

        $success['token'] =  $user->createToken('LoginApp')-> accessToken;

        return response()->json(['success' => $success], Response::HTTP_OK);
    }

    protected function forgetPassword(Request $request)
    {
        if (!app(AuthenticationService::class)->checkEmailExist($request->email)) {
            return response()->json(['error' => 'Email Not Exist'], 401);
        }

        $user = app(User::class)->where('email', '=', $request->email)->first();

        if ($user) {
            $password = str_random(8);
            $user->password = bcrypt($password);
            $user->save();

            $email_description = "This is your new password: $password. Please login to the website to reset to another password.";
            app(AuthenticationService::class)->sendEmail('Forget Password', $email_description, $request->email);

            $success = "true";

            return response()->json(['success' => $success], Response::HTTP_OK);
        }

        return response()->json(['error' => 'ERRORRR'], 401);
    }

    /**
     * Verify the tokwn and email is match
     *
     * @param Request $request
     * @return void
     */
    public function verifyToken(Request $request)
    {
        $user = DB::table('users')->where('email', '=', $request->email)->where('token', '=', '$request->token')->first();

        if ($user) {
            return response()->json(['success' => true], Response::HTTP_OK);
        }

        return response()->json(['error' => 'ERRORRR'], 401);
    }
}
