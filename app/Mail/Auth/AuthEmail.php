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
        $subject = 'Authentication Subject Email';
        $name = 'Alan Tan';

        //Add your views path here
        return $this->view('emails.auth.authemail')
                    ->from($address, $name)
                    ->cc($address, $name)
                    ->bcc($address, $name)
                    ->replyTo($address, $name)
                    ->subject($subject)
                    ->with([
                        'title' => $this->data['title'],
                        'email_message' => $this->data['message']
                    ]);
    }
}
