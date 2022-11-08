<?php

namespace App\Services;

use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Methods\Message;
use Telegram\Bot\Api;

class TelegramMessageHandleService
{
    use Message;

    /**
     * @throws TelegramSDKException
     */
    public function handle($updates): bool
    {
        $telegram = new Api();
        $chatId = $updates['message']['chat']['id'];
        $text = $updates['message']['text'];

        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Your valoper: '.$text
        ]);
        return true;
    }
}
