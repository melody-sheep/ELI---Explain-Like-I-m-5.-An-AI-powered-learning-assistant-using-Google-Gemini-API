<?php
namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;

trait GetCurrentUserId
{
    private function getCurrentUserId()
    {
        if (Auth::check()) {
            return Auth::id();
        }
        
        if (Session::has('guest_user_id')) {
            $guestId = Session::get('guest_user_id');
            if (User::where('id', $guestId)->where('is_guest', true)->exists()) {
                return $guestId;
            }
        }
        
        $sessionId = Session::getId();
        $uniqueId = substr(md5($sessionId . time()), 0, 8);
        
        $user = User::create([
            'name' => 'Guest_' . $uniqueId,
            'email' => 'guest_' . $sessionId . '_' . $uniqueId . '@temp.local',
            'password' => bcrypt(\Illuminate\Support\Str::random(40)),
            'is_guest' => true
        ]);
        
        Session::put('guest_user_id', $user->id);
        Session::put('is_guest', true);
        
        return $user->id;
    }
}