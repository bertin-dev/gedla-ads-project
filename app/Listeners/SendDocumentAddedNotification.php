<?php

namespace App\Listeners;

use App\Events\DocumentAdded;
use App\Notifications\sendLocalNotificaion;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendDocumentAddedNotification
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
    public function handle(DocumentAdded $event)
    {
        $detailsMedia = $event->detailsMedia;
        \Notification::send(auth()->user(), new sendLocalNotificaion($detailsMedia));
        //or
        //auth()->user()->notify(new sendLocalNotificaion($detailsMedia));
        Log::info('Document '.$detailsMedia['media_name'].' ajouté avec succès');
    }
}
