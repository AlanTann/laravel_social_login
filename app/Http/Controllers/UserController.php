<?php
namespace App\Http\Controllers;

use DB;
use Validator;
use Socialite;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public $successStatus = 200;


    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')-> accessToken;
            return response()->json(['success' => $success], $this-> successStatus);
        }
        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
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
    }

    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function details()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], $this-> successStatus);
    }

    public function redirect(string $login_type)
    {
        try{
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
        $access_token = Auth::user()->token();

        //logout from all device if the logout type is all
        if($logout_type == "all") {
            DB::table('oauth_refresh_tokens')
                ->where('access_token_id', $access_token->id)
                ->update([
                    'revoked' => true
                ]);
        }

        $access_token->revoke();
        return 'logged out';
    }

    /**
     * Reset the given user's email.
     *
     * @param  Request $request
     */
    protected function resetEmail(Request $request)
    {
        $user = Auth::user();
        $user->email = $request->email;
        $user->save();

        $success['token'] =  $user->createToken('MyApp')-> accessToken;

        return response()->json(['success' => $success], Response::HTTP_OK);
    }


    public function callback(string $login_type)
    {
        try {
            // var_dump('hi');exit();
            $user = Socialite::driver('github')->stateless()->user();
            // $user = Socialite::driver('github')->user();

            return $user;

            // $user = Socialite::driver($login_type)->user();
            // var_dump($user);exit();
            // $existUser = User::where('email',$googleUser->email)->first();

            // if($existUser) {
            //     Auth::loginUsingId($existUser->id);
            // }
            // else {
            //     $user = new User;
            //     $user->name = $googleUser->name;
            //     $user->email = $googleUser->email;
            //     $user->google_id = $googleUser->id;
            //     $user->password = md5(rand(1,10000));
            //     $user->save();
            //     Auth::loginUsingId($user->id);
            // }
            // return redirect()->to('/home');
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
