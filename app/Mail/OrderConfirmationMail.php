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

    public $order,$user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order, $user)
    {
        $this->order = $order;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "E-Commerce Checkout - Virgin Farms Inc. / Order: WO".$this->order->id;

        return $this->markdown('mail.order.confirmation')
            ->with([
                'user' => $this->user,
                'order' => $this->order,
            ])->subject($subject);
    }
}
