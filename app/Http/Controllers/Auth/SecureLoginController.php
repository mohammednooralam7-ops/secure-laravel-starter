<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SecureLoginController extends Controller
{
    private const MAX_FAILS = 5;
    private const LOCK_MINUTES = 15;

    public function login(Request $request)
    {
        $data = $request->validate(['email' => 'required|email', 'password' => 'required|string']);
        $user = User::where('email', $data['email'])->first();

        if ($user && $user->locked_until && now()->lt($user->locked_until)) {
            AuditLogger::log('login.locked', $user->id);
            return response()->json(['message' => 'Account temporarily locked'], 423);
        }

        // same generic error whether the email exists or not (no user enumeration)
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            if ($user) {
                $user->failed_logins++;
                if ($user->failed_logins >= self::MAX_FAILS) {
                    $user->locked_until = now()->addMinutes(self::LOCK_MINUTES);
                    $user->failed_logins = 0;
                }
                $user->save();
            }
            AuditLogger::log('login.failed', $user?->id, ['email' => $data['email']]);
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        $user->forceFill(['failed_logins' => 0, 'locked_until' => null])->save();
        Auth::login($user);
        $request->session()->regenerate(); // prevent session fixation
        AuditLogger::log('login.success', $user->id);

        return response()->json(['message' => 'ok']);
    }
}
