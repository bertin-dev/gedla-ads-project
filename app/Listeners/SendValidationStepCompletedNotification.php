<?php

namespace App\Listeners;

use App\Events\validationStepCompleted;
use App\Notifications\sendLocalNotificaion;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendValidationStepCompletedNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(validationStepCompleted $event)
    {
        $validationStepDetailsMedia = $event->validationStepDetailsMedia;
        \Notification::send($validationStepDetailsMedia['user'], new sendLocalNotificaion($validationStepDetailsMedia));
        Log::info($validationStepDetailsMedia['body']);
    }
}
