<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserBranch
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role === 'super_admin' || $user->role === 'student') {
            return $next($request);
        }

        if (!$request->isMethod('get') && $request->route()->hasParameter('room')) {
            $room = $request->route('room');

            if (!$room) {
                abort(404);
            }

            $branchId = $room->floor->branch_id;

            if (!$user->branches()->where('branches.id', $branchId)->exists()) {
                abort(403, __('Unauthorized'));
            }
        }

        return $next($request);
    }
}
