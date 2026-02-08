<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\User;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    // OTP expiration time in minutes
    private const OTP_EXPIRY_MINUTES = 10;

    // Maximum verification attempts
    private const MAX_ATTEMPTS = 5;

    /**
     * Show the form to request password reset (enter email)
     */
    public function showRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Send OTP to user's email
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Rate limiting: max 3 requests per minute per IP
        $key = 'password-reset-' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "กรุณารอ {$seconds} วินาที ก่อนลองใหม่อีกครั้ง"
            ]);
        }
        RateLimiter::hit($key, 60);

        // Check if user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // For security, don't reveal if email exists or not
            // But in this case, we'll show a message for better UX
            return back()->withErrors([
                'email' => 'ไม่พบอีเมลนี้ในระบบ'
            ])->withInput();
        }

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Delete old tokens for this email
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Store hashed OTP
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'otp_hash' => Hash::make($otp),
            'attempts' => 0,
            'expires_at' => Carbon::now()->addMinutes(self::OTP_EXPIRY_MINUTES),
            'created_at' => Carbon::now(),
        ]);

        // Send OTP via email
        try {
            Mail::send('emails.otp', ['otp' => $otp, 'user' => $user], function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('รหัส OTP สำหรับรีเซ็ตรหัสผ่าน - JJ Apartment');
            });
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'ไม่สามารถส่งอีเมลได้ กรุณาลองใหม่อีกครั้ง'
            ])->withInput();
        }

        // Store email in session for next step
        session(['password_reset_email' => $request->email]);

        return redirect()->route('password.otp');
    }

    /**
     * Show OTP verification form
     */
    public function showOtpForm()
    {
        if (!session('password_reset_email')) {
            return redirect()->route('password.request');
        }

        return view('auth.passwords.otp');
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $email = session('password_reset_email');

        if (!$email) {
            return redirect()->route('password.request');
        }

        // Get the reset token
        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetToken) {
            return back()->withErrors([
                'otp' => 'ไม่พบข้อมูลการขอรีเซ็ตรหัสผ่าน กรุณาเริ่มต้นใหม่'
            ]);
        }

        // Check if expired
        if (Carbon::parse($resetToken->expires_at)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            session()->forget('password_reset_email');
            return redirect()->route('password.request')->withErrors([
                'email' => 'รหัส OTP หมดอายุแล้ว กรุณาขอรหัสใหม่'
            ]);
        }

        // Check attempts
        if ($resetToken->attempts >= self::MAX_ATTEMPTS) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            session()->forget('password_reset_email');
            return redirect()->route('password.request')->withErrors([
                'email' => 'ลองผิดเกินจำนวนครั้งที่กำหนด กรุณาขอรหัสใหม่'
            ]);
        }

        // Verify OTP
        if (!Hash::check($request->otp, $resetToken->otp_hash)) {
            // Increment attempts
            DB::table('password_reset_tokens')
                ->where('email', $email)
                ->increment('attempts');

            $remainingAttempts = self::MAX_ATTEMPTS - $resetToken->attempts - 1;

            return back()->withErrors([
                'otp' => "รหัส OTP ไม่ถูกต้อง (เหลือ {$remainingAttempts} ครั้ง)"
            ]);
        }

        // OTP verified - mark session
        session(['password_reset_verified' => true]);

        return redirect()->route('password.reset');
    }

    /**
     * Show reset password form
     */
    public function showResetForm()
    {
        if (!session('password_reset_email') || !session('password_reset_verified')) {
            return redirect()->route('password.request');
        }

        return view('auth.passwords.reset');
    }

    /**
     * Reset the password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $email = session('password_reset_email');

        if (!$email || !session('password_reset_verified')) {
            return redirect()->route('password.request');
        }

        // Update user password
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'ไม่พบผู้ใช้งาน'
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Clean up
        DB::table('password_reset_tokens')->where('email', $email)->delete();
        session()->forget(['password_reset_email', 'password_reset_verified']);

        return redirect()->route('login')->with('success', 'เปลี่ยนรหัสผ่านสำเร็จ กรุณาเข้าสู่ระบบใหม่');
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $email = session('password_reset_email');

        if (!$email) {
            return redirect()->route('password.request');
        }

        // Rate limiting
        $key = 'resend-otp-' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 2)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'otp' => "กรุณารอ {$seconds} วินาที ก่อนส่งรหัสใหม่"
            ]);
        }
        RateLimiter::hit($key, 60);

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request');
        }

        // Generate new OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Update token
        DB::table('password_reset_tokens')->where('email', $email)->update([
            'otp_hash' => Hash::make($otp),
            'attempts' => 0,
            'expires_at' => Carbon::now()->addMinutes(self::OTP_EXPIRY_MINUTES),
        ]);

        // Send new OTP
        try {
            Mail::send('emails.otp', ['otp' => $otp, 'user' => $user], function ($message) use ($email) {
                $message->to($email)
                        ->subject('รหัส OTP สำหรับรีเซ็ตรหัสผ่าน - JJ Apartment');
            });
        } catch (\Exception $e) {
            return back()->withErrors([
                'otp' => 'ไม่สามารถส่งอีเมลได้ กรุณาลองใหม่อีกครั้ง'
            ]);
        }

        return back()->with('success', 'ส่งรหัส OTP ใหม่เรียบร้อยแล้ว');
    }
}
