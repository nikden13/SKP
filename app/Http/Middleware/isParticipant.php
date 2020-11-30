<?php

namespace App\Http\Middleware;

use App\Models\Event;
use Closure;
use Illuminate\Http\Request;

class isParticipant
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
        $event_from_url = Event::findOrFail($request->route('event')->id);
        $event = auth()->user()->events->where('pivot.event_id', $event_from_url->id)->first();
        if (!$event)
            return response()->json(['message' => 'Only available to the participant'],403);
        return $next($request);
    }
}
