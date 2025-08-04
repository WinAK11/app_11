<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPlacedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $cart;

    /**
     * Create a new message instance.
     */
    public function __construct($order, $cart)
    {
        $this->order = $order;
        $this->cart = $cart;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Order Confirmation')
            ->view('emails.order_placed');
    }
}
