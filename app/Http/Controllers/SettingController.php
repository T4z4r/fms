<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'ai_api_key' => Setting::get('ai_api_key'),
            'ai_model' => Setting::get('ai_model', 'gpt-4'),
            'ai_endpoint' => Setting::get('ai_endpoint', 'https://api.openai.com/v1'),
            'company_name' => Setting::get('company_name', ''),
            'company_currency' => Setting::get('company_currency', 'GBP'),
            'company_date_format' => Setting::get('company_date_format', 'Y-m-d'),
            'company_fiscal_year_start' => Setting::get('company_fiscal_year_start', 'January'),
        ];

        return view('settings.index', compact('settings'));
    }

    public function profile()
    {
        $user = Auth::user();

        return view('settings.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'ai_api_key' => 'nullable|string',
            'ai_model' => 'nullable|string',
            'ai_endpoint' => 'nullable|url',
            'company_name' => 'nullable|string|max:255',
            'company_currency' => 'nullable|string|max:10',
            'company_date_format' => 'nullable|string|max:20',
            'company_fiscal_year_start' => 'nullable|string|max:20',
        ]);

        if ($request->filled('ai_api_key')) {
            Setting::set('ai_api_key', $request->ai_api_key);
        }

        if ($request->filled('ai_model')) {
            Setting::set('ai_model', $request->ai_model);
        }

        if ($request->filled('ai_endpoint')) {
            Setting::set('ai_endpoint', $request->ai_endpoint);
        }

        if ($request->filled('company_name')) {
            Setting::set('company_name', $request->company_name);
        }

        if ($request->filled('company_currency')) {
            Setting::set('company_currency', $request->company_currency);
        }

        if ($request->filled('company_date_format')) {
            Setting::set('company_date_format', $request->company_date_format);
        }

        if ($request->filled('company_fiscal_year_start')) {
            Setting::set('company_fiscal_year_start', $request->company_fiscal_year_start);
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Password changed successfully.');
    }
}
