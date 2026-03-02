<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProductExportFailedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $errorMessage
    ) {
    }

    public function build()
    {
        return $this->subject('Product export failed')
            ->view('emails.export_failed')
            ->with([
                'user' => $this->user,
                'error' => $this->errorMessage,
            ]);
    }
}
