<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class SendTestEmail extends Mailable
{
    use Queueable, SerializesModels;
    private $name,$users;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->users = User::all();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //return $this->view('view.name');
        return $this->from('no-reply@test.com')
            ->subject('Test Email')
            ->view('emails.test')
            ->with([
                'myName'  =>  $this->name,
                'users'  => $this->users
            ]);
    }
}
