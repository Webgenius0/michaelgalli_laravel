<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportMail extends Mailable implements ShouldQueue
{

    use Queueable, SerializesModels;

    public $name;
    public $phone;
    public $email;
    public $userMessage; // Renamed from $message

    /**
     * Create a new message instance.
     */
    public function __construct(string $name, string $email, string $userMessage, string $phone)
    {
        $this->name        = $name;
        $this->email       = $email;
        $this->userMessage = $userMessage;
        $this->phone       = $phone;
    }

    public function build()
    {
        return $this->subject('🚨 User needs help')
            ->view('mail.support_mail')
            ->with([
                'name'        => $this->name,
                'email'       => $this->email,
                'phone'       => $this->phone,
                'userMessage' => $this->userMessage,
            ]);
    }

}
