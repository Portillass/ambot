<?php

namespace App\Mail;

use App\Models\PendingAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountPending extends Mailable
{
    use Queueable, SerializesModels;

    public $pendingAccount;

    /**
     * Create a new message instance.
     */
    public function __construct(PendingAccount $pendingAccount)
    {
        $this->pendingAccount = $pendingAccount;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('New Account Pending Approval')
                    ->view('emails.account-pending')
                    ->with(['pendingAccount' => $this->pendingAccount]);
    }
}
