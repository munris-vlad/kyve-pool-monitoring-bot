<?php

use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Log;
use App\Services\TelegramMessageHandleService;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/' . env('TELEGRAM_TOKEN') . '/webhook', function () {
    $updates = Telegram::commandsHandler(true);
    $tgMessageService = new TelegramMessageHandleService();
    $response = $tgMessageService->handle($updates);
    return $updates;
});
