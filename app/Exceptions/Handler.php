<?php

namespace App\Exceptions;

use App\Http\Controllers\BotController;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

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

    public function sendTelegramMessage(Throwable $exception)
    {
        try {

            $content['message'] = $exception->getMessage();

            BotController::sendMessage('-1001995750232', $content);

        } catch (Throwable $exception) {
            Log::error($exception);
        }
    }
}
