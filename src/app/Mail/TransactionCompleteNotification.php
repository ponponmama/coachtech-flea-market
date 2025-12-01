<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Item;

class TransactionCompleteNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $seller;
    public $buyer;
    public $item;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $seller, User $buyer, Item $item)
    {
        $this->seller = $seller;
        $this->buyer = $buyer;
        $this->item = $item;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('取引が完了しました')
                    ->from('noreply@coachtech-flea-market.com', 'CoachTech Flea Market')
                    ->view('emails.transaction-complete');
    }
}
