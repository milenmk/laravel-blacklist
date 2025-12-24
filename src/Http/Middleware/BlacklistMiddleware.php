<?php

declare(strict_types=1);

namespace Milenmk\LaravelBlacklist\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Milenmk\LaravelBlacklist\Services\BlacklistService;

final class BlacklistMiddleware
{
    public function handle(Request $request, Closure $next, ?string $defaultContext = null)
    {
        $service = app(BlacklistService::class);

        foreach ($request->all() as $field => $value) {
            if (! is_string($value)) {
                continue;
            }

            $context = $defaultContext ?? $field;

            $result = $service->checkValue($value, $context);

            if (! $result->isClean()) {
                abort(422, __('blacklist::validation.blocked'));
            }
        }

        return $next($request);
    }
}
