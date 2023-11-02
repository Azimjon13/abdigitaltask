<?php

namespace App\Exceptions;

use App\Http\Controllers\BotController;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Telegram\Bot\Laravel\Facades\Telegram;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            $this->sendTelegramMessage($e);
        });
    }


    public function sendTelegramMessage(Throwable $e)
    {
        try {

            $content = $e->getMessage();
            Telegram::sendMessage([
                'chat_id' => '-1001995750232',
                'parse_mode' => 'HTML',
                'text' => $content
            ]);
            //BotController::sendMessage('-1001995750232', $content);

        } catch (Throwable $e) {
            Log::error($e);
        }
    }
}
