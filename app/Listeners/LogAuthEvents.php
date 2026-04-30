<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;

class LogAuthEvents
{
    private static bool $loginLogged = false;
    private static bool $logoutLogged = false;

    public function __construct(private Request $request) {}

    public function handleLogin(Login $event): void
    {
        if (self::$loginLogged) {
            return;
        }
        self::$loginLogged = true;

        activity('auth')
            ->causedBy($event->user)
            ->withProperties($this->context())
            ->log('logged in');
    }

    public function handleLogout(Logout $event): void
    {
        if (! $event->user || self::$logoutLogged) {
            return;
        }
        self::$logoutLogged = true;

        activity('auth')
            ->causedBy($event->user)
            ->withProperties($this->context())
            ->log('logged out');
    }

    public function handleFailed(Failed $event): void
    {
        activity('auth')
            ->withProperties(array_merge($this->context(), [
                'email' => $event->credentials['email'] ?? null,
            ]))
            ->log('login failed');
    }

    private function context(): array
    {
        return [
            'ip' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
        ];
    }
}
