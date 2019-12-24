<?php
namespace App\Http\Controllers;

use DB;
use Mail;
use Validator;
use Socialite;
use App\User;
use App\Http\Controllers\Controller;
use App\Mail\Auth\AuthEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function testSendGridEmail() {
        echo 'hi';
        $data = ['message' => 'This is a test!'];

        Mail::to('tan_alan1020@hotmail.com')->send(new AuthEmail($data));
    }
}
