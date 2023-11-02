<?php

namespace App\Http\Controllers;

use Telegram\Bot\Api;

class BotController extends Controller
{
    protected $telegram;

    /**
     * Create a new controller instance.
     *
     * @param  Api  $telegram
     */
    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    public function sendMessage($chat_id, $content){

        $this->telegram->sendMessage([
            'chat_id' => $chat_id,
            'text' => $content
        ]);
    }
    /**
     * Show the bot information.
     */
    public function show()
    {
        $response = $this->telegram->getMe();

        return $response;
    }
}
