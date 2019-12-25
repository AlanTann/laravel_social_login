<?php
namespace App\Http\Controllers;

use DB;
use Mail;
use Validator;
use Socialite;
use App\User;
use App\Mail\Auth\AuthEmail;
use App\Http\Controllers\Controller;
// use Illuminate\Notifications\Notifiable;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

// class UserController extends Authenticatable implements MustVerifyEmail
class UserController extends Controller
{
    use Notifiable;

    public $successStatus = 200;


    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        try {
            if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
                $user = Auth::user();
                $success['token'] =  $user->createToken('MyApp')-> accessToken;
                return response()->json(['success' => $success], $this-> successStatus);
            } else {
                return response()->json(['error'=>'Unauthorised'], 401);
            }
        } catch (\Exception $ex) {
            return response()->json([
                'errors' => [
                    'code' => $ex->getCode(),
                    'title' => 'Internal Server Error',
                    'detail' => $ex->getMessage(),
                ],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
                'c_password' => 'required|same:password',
            ]);

            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);
            }

            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            $success['token'] =  $user->createToken('MyApp')-> accessToken;
            $success['name'] =  $user->name;

            return response()->json(['success'=>$success], $this-> successStatus);
        } catch (\Exception $ex) {
            return response()->json([
                'errors' => [
                    'code' => $ex->getCode(),
                    'title' => 'Internal Server Error',
                    'detail' => $ex->getMessage(),
                ],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function details()
    {
        try {
            $user = Auth::user();
            return response()->json(['success' => $user], $this-> successStatus);
        } catch (\Exception $ex) {
            return response()->json([
                'errors' => [
                    'code' => $ex->getCode(),
                    'title' => 'Internal Server Error',
                    'detail' => $ex->getMessage(),
                ],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function redirect(string $login_type)
    {
        try {
            return Socialite::driver($login_type)->stateless()->redirect();
        } catch (\Exception $ex) {
            return response()->json([
                'errors' => [
                    'code' => $ex->getCode(),
                    'title' => 'Internal Server Error',
                    'detail' => $ex->getMessage(),
                ],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function logout(string $logout_type)
    {
        try {
            $access_token = Auth::user()->token();

            //logout from all device if the logout type is all
            if ($logout_type == "all") {
                DB::table('oauth_refresh_tokens')
                    ->where('access_token_id', $access_token->id)
                    ->update([
                        'revoked' => true
                    ]);
            }

            $access_token->revoke();
            return 'logged out';
        } catch (\Exception $ex) {
            return response()->json([
                'errors' => [
                    'code' => $ex->getCode(),
                    'title' => 'Internal Server Error',
                    'detail' => $ex->getMessage(),
                ],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Reset the given user's email.
     *
     * @param  Request $request
     */
    protected function resetEmail(Request $request)
    {
        try {
            $user = Auth::user();
            $user->email = $request->email;
            $user->save();

            $success['token'] =  $user->createToken('MyApp')-> accessToken;

            return response()->json(['success' => $success], Response::HTTP_OK);
        } catch (\Exception $ex) {
            return response()->json([
                'errors' => [
                    'code' => $ex->getCode(),
                    'title' => 'Internal Server Error',
                    'detail' => $ex->getMessage(),
                ],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function callback(string $login_type)
    {
        try {
            $socialite_user = Socialite::driver($login_type)->stateless()->user();

            $exist_user = User::where('email', $socialite_user->email)->first();

            if ($exist_user) {
                $success['token'] =  $exist_user->createToken('MyApp')-> accessToken;
            } else {
                $user = new User;
                $user->name = $socialite_user->name;
                $user->email = $socialite_user->email;
                // $user->google_id = $socialite_user->id;
                $user->password = bcrypt(rand(1, 10000));
                $user->save();
                $success['token'] =  $user->createToken('MyApp')-> accessToken;
            }

            return response()->json(['success' => $success], $this-> successStatus);
        } catch (\Exception $ex) {
            return response()->json([
                'errors' => [
                    'code' => $ex->getCode(),
                    'title' => 'Internal Server Error',
                    'detail' => $ex->getMessage(),
                ],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
