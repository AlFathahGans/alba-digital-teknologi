<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting('Selamat Datang!')
            ->line('Ini adalah isi dari email Anda.')
            ->action('Silahkan kunjungi', url('/'))
            ->line('Terima kasih telah menggunakan aplikasi kami!')
            ->salutation('Salam, Abdul Fathah');
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

