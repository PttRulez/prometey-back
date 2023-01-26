<?php

namespace App\Notifications\Telegram;

use Carbon\Carbon;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramFile;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class Delimeter extends Notification
{
    use Queueable;

    protected $gifs = [
        'https://clipchamp.com/static/88bb8fc56d817b40f1772ad4e615eaae/Simpson-GIF.gif',
        'https://c.tenor.com/70aU0bi1JNUAAAAM/chips-poker-chips.gif',
        'https://i.pinimg.com/originals/80/63/af/8063afb02cf1195b55be4f72f17276b3.gif',
        'https://i.gifer.com/X2gq.gif',
        'https://j.gifs.com/KdVDR2.gif',
        'https://i.gifer.com/WQgJ.gif'
    ];

    public function __construct()
    {
        //
    }

    public function via($notifiable)
    {
        return [TelegramChannel::class];
    }

    public function toTelegram($notifiable)
    {
        $gifNumber = (int) substr(Carbon::now()->minute, 0, 1);
        $gif = $this->gifs[$gifNumber];
        return TelegramFile::create()
            ->animation($gif)
            ->button('сайт',url('/'));
    }
}
