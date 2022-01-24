<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailDemo extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $EmailData;
    public function __construct($EmailData)
    {
        //
        
        $this->EmailData = $EmailData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
   

      public function build()
    {
        return $this->subject('Mail from ItSolutionStuff.com')
                    ->view('Email.receiveOrderEmail')
                    ->with(
                        [
                            'EmailData' => $this->EmailData
                        ]
                    );

    }
}
