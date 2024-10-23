<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\SendTestEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Mail\Mailable;

class SendEmailTestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $name;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($name)
    {
        $this->name = $name;
      /*   $emailTo = 'me@gmail.com';
        $destinations = ['abc@gmail.com','bcd@gmail.com'];
        $enviarATodos = true; */
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = new SendTestEmail($this->name);
        Mail::to('digitalestalentos3@gmail.com')->send($email);
/*         if($this->enviarATodos)
            Mail::to($emailTo)->bcc($destinations)->send($email);
        Mail::to($emailTo)->send($email); */
    }
}
