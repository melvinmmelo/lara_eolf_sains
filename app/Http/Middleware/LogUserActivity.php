<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    private const SKIP_PATH_PREFIXES = [
        'up',
        'livewire',
        '_debugbar',
        'build',
        'storage',
    ];

    private const SKIP_ROUTE_NAMES = [
        // High-frequency AJAX polling endpoints can be added here later
        // if the activity_log table grows too fast.
    ];

    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        if (! auth()->check()) {
            return;
        }

        if ($this->shouldSkip($request)) {
            return;
        }

        $user = auth()->user();
        $route = $request->route();
        $routeName = $route?->getName();

        activity('page-visit')
            ->causedBy($user)
            ->withProperties([
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'path' => $request->path(),
                'route' => $routeName,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'branch_code' => session('branch_code'),
                'status' => $response->getStatusCode(),
            ])
            ->log("{$request->method()} /{$request->path()}");

        $this->touchLastActiveAt($user);
    }

    private function shouldSkip(Request $request): bool
    {
        if (in_array($request->method(), ['HEAD', 'OPTIONS'], true)) {
            return true;
        }

        $path = $request->path();
        foreach (self::SKIP_PATH_PREFIXES as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                return true;
            }
        }

        $routeName = $request->route()?->getName();
        if ($routeName && in_array($routeName, self::SKIP_ROUTE_NAMES, true)) {
            return true;
        }

        return false;
    }

    private function touchLastActiveAt($user): void
    {
        $cacheKey = "user-last-active-{$user->id}";

        if (Cache::has($cacheKey)) {
            return;
        }

        $user->forceFill(['last_active_at' => now()])->saveQuietly();
        Cache::put($cacheKey, true, 60);
    }
}
