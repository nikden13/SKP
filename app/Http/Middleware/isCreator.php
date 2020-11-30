<?php

namespace App\Http\Middleware;

use App\Models\Event;
use Closure;
use Illuminate\Http\Request;

class isCreator
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
        $user = $event->users->where('pivot.role', 'creator')->first();
        if (auth()->user()->id != $user->id)
            return response()->json(['message' => 'Only available to the creator'],403);
        return $next($request);
    }
}
