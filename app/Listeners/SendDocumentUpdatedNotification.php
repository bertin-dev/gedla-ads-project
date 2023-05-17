<?php

namespace App\Listeners;

use App\Events\DocumentUpdated;
use App\Notifications\sendLocalNotificaion;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendDocumentUpdatedNotification
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
    public function handle(DocumentUpdated $event)
    {
        $detailsMedia = $event->detailsMedia;
        $detailsMedia['user']->notify(new sendLocalNotificaion($detailsMedia));
        Log::info($detailsMedia['body']);
    }
}
