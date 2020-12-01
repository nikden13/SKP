<?php

namespace App\Http\Middleware;

use App\Models\Event;
use Closure;
use Illuminate\Http\Request;

class Lock
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
        $lock = auth()->user()->events
            ->where('pivot.event_id', $event->id)
            ->where('pivot.lock', true)
            ->first();
        if ($lock) {
            return response()->json(['message' => 'You have already submitted your answer'], 403);
        }
        return $next($request);
    }
}
