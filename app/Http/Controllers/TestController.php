<?php
namespace App\Http\Controllers;

use DB;
use Mail;
use Validator;
use Socialite;
use App\User;
use UrlSigner;
use App\Http\Controllers\Controller;
use App\Mail\Auth\AuthEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function testSendGridEmail()
    {
        $data = [
            'title' => 'Title test',
            'message' => 'This is a test!'
        ];

        Mail::to('tan_alan1020@hotmail.com')->send(new AuthEmail($data));
        return response()->json(['success'=>true], Response::HTTP_OK);
    }

    public function testVerifySignature()
    {
        var_dump(Url::signedRoute('verifyEmail', ['email' => 'harold3@gmail.com']));
        exit;
    }
}
