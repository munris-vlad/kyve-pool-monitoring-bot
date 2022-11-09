<?php

namespace App\Console\Commands;

use App\Models\UserValoper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class MonitoringCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitoring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kyve pool monitoring by users addresses';

    protected string $apiUrl = 'https://api.beta.kyve.network/kyve/query/v1beta1/staker/';
    protected int $decimals = 1000000000;
    protected int $fundsAlertAmount = 100;

    /**
     * Execute the console command.
     *
     * @return bool
     * @throws \JsonException
     * @throws GuzzleException|TelegramSDKException
     */
    public function handle(): bool
    {
        $addresses = UserValoper::all();
        $client = new Client();

        foreach ($addresses as $address) {
            try {
                $info = $client->request('GET', $this->apiUrl . $address->valoper)->getBody()->getContents();
                $info = json_decode($info, true, 512, JSON_THROW_ON_ERROR);
            } catch (\GuzzleHttp\Exception\ServerException) {
                continue;
            }

            $moniker = $info['staker']['metadata']['moniker'];
            $pools = $info['staker']['pools'];
            foreach ($pools as $pool) {
                $poolName = $pool['pool']['name'];
                $balance = $this->sharesToDecimal((int)$pool['balance']);
                if ($balance < $this->fundsAlertAmount) {
                    $alert = "<b>FUNDS ALERT</b>\n";
                    $text = "<code>
                        Validator... ".$moniker."
                        Pool........ ".$poolName."
                        Balance..... ".$balance." \$KYVE
                        </code>
                    ";
                    $this->sendMessage($address->telegram_id, $alert . str_replace(' ', '', $text));
                }

                if ($pool['pool']['status'] !== 'POOL_STATUS_ACTIVE') {
                    $alert = "<b>STATUS ALERT</b>\n";
                    $text = "<code>
                        Validator... ".$moniker."
                        Pool........ ".$poolName."
                        Status...... ".$pool['pool']['status']."
                        </code>
                    ";
                    $this->sendMessage($address->telegram_id, $alert . str_replace(' ', '', $text));
                }

                if ((int)$pool['points'] > 0) {
                    $alert = "<b>POINTS ALERT</b>\n";
                    $text = "<code>
                        Validator... ".$moniker."
                        Pool........ ".$poolName."
                        Points...... ".$pool['points']."
                        </code>
                    ";
                    $this->sendMessage($address->telegram_id, $alert . str_replace(' ', '', $text));
                }
            }
        }

        return Command::SUCCESS;
    }

    /**
     * @throws TelegramSDKException
     */
    public function sendMessage($chat, $text)
    {
        $telegram = new Api();
        $telegram->sendMessage([
            'chat_id' => $chat,
            'text' => $text,
            'parse_mode' => 'HTML'
        ]);
    }

    public function sharesToDecimal($shares)
    {
        return round((float)$shares * (1/$this->decimals), 2);
    }
}
