<?php
// app/Http/Middleware/GuestMode.php (NEW FILE)

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class GuestMode
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() && !Session::has('guest_id')) {
            // Create a unique guest user
            $guestId = Session::getId();
            $user = \App\Models\User::create([
                'name' => 'Guest_' . substr($guestId, 0, 8),
                'email' => 'guest_' . $guestId . '@temp.local',
                'password' => bcrypt(\Illuminate\Support\Str::random(40)),
                'is_guest' => true
            ]);
            
            Session::put('guest_id', $user->id);
            Session::put('is_guest', true);
        }
        
        return $next($request);
    }
}