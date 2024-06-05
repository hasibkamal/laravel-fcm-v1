<?php

namespace HasibKamal\LaravelFCM\Listeners;

use HasibKamal\LaravelFCM\FCM\FCMClient;
use HasibKamal\LaravelFCM\Events\PushNotificationFCM;

class SendNotification
{
    protected $fcm;

    public function __construct(FCMClient $fcm)
    {
        $this->fcm = $fcm;
    }

    public function handle(PushNotificationFCM $event)
    {
        $this->fcm->sendNotification($event->tokens, $event->notification);
    }
}
