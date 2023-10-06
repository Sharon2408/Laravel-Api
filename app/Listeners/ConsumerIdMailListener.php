<?php

namespace App\Listeners;

use App\Events\ConsumerIdMailEvent;
use App\Mail\ConsumerIdMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class ConsumerIdMailListener implements ShouldQueue
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
    public function handle(ConsumerIdMailEvent $event): void
    {
         $data = $event->data;

         Mail::to($data['email'])->send(new ConsumerIdMail($data));
    }
}
