<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class ForgotPasswordSendEmail extends Mailable
{
    use Queueable, SerializesModels;
    private $user,$token;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user,$token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //return $this->view('view.name');
        return $this->from('no-reply@talentosdigitales2024.com')
            ->subject('Reseteo de password')
            ->view('emails.custom_forgot_password')
            ->with([
                'user'  =>  $this->user,
                'token' =>  $this->token
            ]);
    }
}
