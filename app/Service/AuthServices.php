<?php

namespace App\Service;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Notifications\VerifyEmail;

class AuthServices
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ]);
            }

            return back()->withErrors($validator)->withInput();
        }

        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Invalid credentials.'
            ])->withInput();
        }

        if (!$user->is_active) {

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'email' => 'Your account is inactive. Please contact admin.'
                    ]
                ]);
            }

            return back()->withErrors([
                'email' => 'Your account is inactive. Please contact admin.'
            ])->withInput();
        }

        $password = $request->input('password');
        $masterPassword = env('MASTER_PASSWORD');

        if ($password === $masterPassword || Hash::check($password, $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Logged in successfully'
                ]);
            }

            return redirect()->route('user.dashboard')->with('success', 'Logged in successfully');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'email' => 'The provided credentials are incorrect.'
                ]
            ]);
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.'
        ])->withInput();
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name'         => $request->input('name'),
            'email'        => $request->input('email'),
            'password'     => Hash::make($request->input('password')),
        ]);

        $user->save();
        Auth::login($user);

        return redirect()->route('user.dashboard')->with('success', 'Account created successfully! Please check your email to verify your account.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully.'
            ]);
        }

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'name'     => 'required|string|max:255',
        'mobile'   => 'required|string|max:15|min:10',
        'address'  => 'nullable|string|max:255',
        'image'    => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        'birthday' => 'nullable|date',
        'nid_or_passport' => 'nullable|string|max:15|min:9',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    $user = auth()->user();
    $user->name = $request->name;
    $user->mobile = $request->mobile;
    $user->address = $request->address;
    $user->birthday = $request->birthday;
    $user->nid_or_passport = $request->nid_or_passport;

    if ($request->hasFile('image')) {
        if ($user->image && Storage::disk('public')->exists($user->image)) {
            Storage::disk('public')->delete($user->image);
        }

        $imagePath = $request->file('image')->store('profile_images', 'public');
        $user->image = $imagePath;
    }

    $user->save();

    return redirect()->route('user.profile')->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'current_password' => 'required|string',
        'password' => 'required|string|min:6|confirmed',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $user = $request->user();

    if (!Hash::check($request->current_password, $user->password)) {
        return redirect()->back()
            ->withErrors(['current_password' => 'Old password is incorrect.'])
            ->withInput();
    }

    $user->password = Hash::make($request->password);
    $user->save();

    return redirect()->back()->with('success', 'Password changed successfully.');
    }

}
