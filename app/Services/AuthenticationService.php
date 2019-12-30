<?php
namespace App\Services;

use Mail;
use App\Mail\Auth\AuthEmail;

class AuthenticationService
{
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
