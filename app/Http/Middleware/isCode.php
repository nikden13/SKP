<?php

namespace App\Http\Middleware;

use App\Models\Event;
use Closure;
use Illuminate\Http\Request;

class isCode
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $event = Event::findOrFail($request->route('event')->id);
        if ($event->check_type == 'test')
            return response()->json(['message' => 'QR-code or CAPTCHA not exists. Need test'],403);
        return $next($request);
    }
}
