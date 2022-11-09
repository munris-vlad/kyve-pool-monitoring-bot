<?php

namespace App\Commands;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "start";

    /**
     * @var string Command Description
     */
    protected $description = "Start Command to get you started";

    /**
     * @inheritdoc
     */
    public function handle(): void
    {
        $text = 'Welcome to Kyve pool monitoring bot! Enter your address for monitoring:';

        $this->replyWithMessage(compact('text'));
    }
}
