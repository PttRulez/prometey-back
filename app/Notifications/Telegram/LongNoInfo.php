<?php

namespace App\Notifications\Telegram;

use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LongNoInfo extends Notification
{
    use Queueable;

    protected $nickname = '';

    public function __construct($nickname)
    {
        $this->nickname = $nickname;
    }

    public function via($notifiable)
    {
        return [TelegramChannel::class];
    }

    public function toTelegram($notifiable)
    {
        return TelegramMessage::create()
            ->content($this->nickname . ' - нет информации от калка');
    }
}
