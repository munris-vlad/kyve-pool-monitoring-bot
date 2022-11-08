<?php

namespace App\Services;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramMessageHandleService
{

    /**
     * @throws TelegramSDKException
     */
    public function handle($updates): bool
    {
        $telegram = new Telegram;
        $chatId = $updates['message']['chat']['id'];
        $text = $updates['message']['text'];
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Your valoper: '.$text
        ]);
        return true;
    }
}
