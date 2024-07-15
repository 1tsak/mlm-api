<?php

namespace App\Http\Controllers;

use App\Models\WebSetting;
use Illuminate\Http\Request;

class WebSettingsController extends Controller
{
    public function getSettings()
    {
        $settings = WebSetting::first();
        return response()->json($settings);
    }

    public function saveSettings(Request $request)
    {

        $request->validate([
            'telegram_share_link' => 'required|string',
            'telegram_chat_link' => 'required|string',
            'refer_bonus' => 'required|numeric',
            'level_percentage' => 'required|numeric'
        ]);

        $settings = WebSetting::first();
        if ($settings) {
            $settings->update($request->all());
        } else {
            $settings = WebSetting::create($request->all());
        }

        return response()->json($settings);
    }
}
