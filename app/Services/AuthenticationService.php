<?php
namespace App\Services;

use Mail;
use App\User;
use App\Mail\Auth\AuthEmail;

// use App\Repositories\UserRepository;
// use App\Repositories\PasswordResetRepository;
class AuthenticationService
{
    public function checkEmailExist(string $email)
    {
        $user = new User();
        $email_exist = $user->where('email', $email)->first();
        return !$email_exist ? false: true;
    }

    public function sendEmail($title, $message, $email_address)
    {
        // $title = "Title test";
        // $message = "This is a test!";
        // $email_address = "tan_alan1020@hotmail.com";

        $data = [
            'title' => $title,
            'message' => $message
        ];

        Mail::to($email_address)->send(new AuthEmail($data));
        return true;
    }
}
