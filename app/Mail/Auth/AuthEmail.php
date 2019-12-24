<?php
namespace App\Mail\Auth;

use App\Abstracts\AbstractMailable;

class AuthEmail extends AbstractMailable
{
    public $data = '';

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        parent::__construct($data);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $address = 'janeexampexample@example.com';
        $subject = 'This is a demo!';
        $name = 'Jane Doe';

        //Add your views path here
        return $this->view('emails.auth.authemail')
                    ->from($address, $name)
                    ->cc($address, $name)
                    ->bcc($address, $name)
                    ->replyTo($address, $name)
                    ->subject($subject)
                    ->with([ 'test_message' => $this->data['message'] ]);
    }
}
