<?php

namespace App\Services;

use App\Models\UserValoper;
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

        if (!str_contains($text, 'valoper') || strlen($text) !== 43) {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Please send a valid validator address.'
            ]);

            return true;
        }

        if (UserValoper::where('chatId', $chatId)->where('valoper', $text)->count()) {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'You have already subscribed to this validator.'
            ]);

            return true;
        }

        UserValoper::create([
            'chatId' => $chatId,
            'valoper' => $text
        ]);

        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Your valoper: '.$text.' successfully added to monitoring list',
        ]);

        return true;
    }
}
