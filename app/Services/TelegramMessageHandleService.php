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

        if (str_contains($text, 'start')) {
            return true;
        }

        if (!str_contains($text, 'kyve') || strlen($text) !== 43) {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Please send a valid address.'
            ]);

            return true;
        }

        if (UserValoper::where('chatId', $chatId)->where('valoper', $text)->count()) {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'You have already subscribed to this address.'
            ]);

            return true;
        }

        UserValoper::create([
            'telegram_id' => $chatId,
            'valoper' => $text
        ]);

        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Your address: '.$text.' successfully added to monitoring list',
        ]);

        return true;
    }
}
