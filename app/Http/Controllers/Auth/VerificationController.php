<?php

namespace App\Http\Controllers\Auth;

use DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verifyUser(Request $request)
    {
        $user = User::where('email', '=', $request->email)->first();

        $user->email_verified_at = Carbon::now();
        $user->save();

        if ($user) {
            return response()->json(['success' => true], Response::HTTP_OK);
        }
    }
}
