<?php

namespace Vanguard\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order,$subject,$user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order,$subject , $user)
    {
        $this->order = $order;
        $this->subject = $subject;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.order.confirmation')
            ->with([
                'user' => $this->user
            ])
            ->subject($this->subject);
    }
}
