<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'ai_api_key' => Setting::get('ai_api_key'),
            'ai_model' => Setting::get('ai_model', 'gpt-4'),
            'ai_endpoint' => Setting::get('ai_endpoint', 'https://api.openai.com/v1'),
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'ai_api_key' => 'nullable|string',
            'ai_model' => 'nullable|string',
            'ai_endpoint' => 'nullable|url',
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

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
