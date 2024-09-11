<?php

namespace App\Listeners;

use App\Events\EmailVerified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Notifications\WelcomeEmailNotification;
use Illuminate\Support\Facades\Log;

class SendWelcomeEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EmailVerified $event): void
    {
        Log::info('Queued: Email verified for user: ' . $event->user->email);
        // Kirim email selamat datang
        $event->user->notify(new WelcomeEmailNotification());
    }
}
