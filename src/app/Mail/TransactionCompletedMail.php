<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionCompletedMail extends Mailable
{
    public $purchase;

    public function __construct($purchase)
    {
        $this->purchase = $purchase;
    }

    public function build()
    {
        return $this->subject('取引が完了しました')
                    ->view('emails.transaction_completed');
    }
}
