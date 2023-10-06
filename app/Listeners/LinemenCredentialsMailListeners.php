<?php

namespace App\Listeners;

use App\Events\LinemenCredentialsMailEvent;
use App\Mail\LinemenCredentialsMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class LinemenCredentialsMailListeners implements ShouldQueue
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
    public function handle(LinemenCredentialsMailEvent $event): void
    {
        $data = $event->data;

         Mail::to($data['email'])->send(new LinemenCredentialsMail($data));
    }
}
