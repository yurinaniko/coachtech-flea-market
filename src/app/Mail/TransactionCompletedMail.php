<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionCompletedMail extends Mailable
{
    use Queueable, SerializesModels;
    public $purchase;
    public $partnerName;
    public function __construct($purchase, $partnerName)
    {
        $this->purchase = $purchase;
        $this->partnerName = $partnerName;
    }

    public function build()
    {
        return $this->subject('取引が完了しました')
                    ->view('emails.transaction_completed')
                    ->with([
                        'purchase' => $this->purchase,
                        'partnerName' => $this->partnerName,
                    ]);

    }
}