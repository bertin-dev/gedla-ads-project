<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class sendLocalNotificaion extends Notification
{
    use Queueable;

    private $detailsMedia;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($detailsMedia)
    {
        $this->detailsMedia = $detailsMedia;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'user' => $this->detailsMedia['user'],
            'subject' => $this->detailsMedia['subject'],
            'body' => $this->detailsMedia['body'],
            'media_id' => $this->detailsMedia['media_id'],
            'media_name' => $this->detailsMedia['media_name'],
            'validation_step_id' => $this->detailsMedia['validation_step_id'],
        ];
    }
}
