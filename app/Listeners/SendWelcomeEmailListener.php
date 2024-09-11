<?php

namespace App\Listeners;

use App\Events\EmailVerified;
use App\Jobs\SendWelcomeEmailJob; // Import job
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWelcomeEmailListener implements ShouldQueue
{
    public function __construct()
    {
        //
    }

    public function handle(EmailVerified $event): void
    {
        dispatch(new SendWelcomeEmailJob($event->user));
    }
}
