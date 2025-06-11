<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeleteAccountVerification extends Notification implements ShouldQueue
{
    use Queueable;

    private $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Konta dzēšanas verifikācijas kods')
            ->greeting('Sveicināti!')
            ->line('Jūs pieprasījāt sava konta dzēšanu.')
            ->line('Jūsu verifikācijas kods ir: ' . $this->code)
            ->line('Šis kods būs derīgs 15 minūtes.')
            ->line('Ja jūs nepieprasījāt konta dzēšanu, lūdzu, ignorējiet šo e-pastu.')
            ->salutation('Ar cieņu,')
            ->salutation('Ceļu Remonti komanda');
    }
} 