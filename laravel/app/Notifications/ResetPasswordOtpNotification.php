<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordOtpNotification extends Notification
{
    use Queueable;

    protected $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function toBatches($notifiable)
    {
        return ['mail'];
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Kode OTP Reset Password EduVan')
            ->greeting('Halo!')
            ->line('Kamu menerima email ini karena ada permintaan untuk mereset password akun EduVan kamu.')
            ->line('Berikut adalah kode OTP rahasia kamu:')
            ->line('## ' . $this->otp) // Gunakan Markdown '##' untuk membuat teks OTP besar dan tebal
            ->line('Kode OTP ini hanya berlaku selama 15 menit ke depan.')
            ->line('Jika kamu tidak merasa melakukan permintaan ini, abaikan saja email ini.');
    }
}
