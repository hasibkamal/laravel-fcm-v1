<?php
namespace HasibKamal\LaravelFCM\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PushNotificationFCM
{
    use Dispatchable, SerializesModels;

    public $tokens;
    public $notification;

    public function __construct(array $tokens, array $notification)
    {
        $this->tokens = $tokens;
        $this->notification = $notification;
    }
}
