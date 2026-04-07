<?php

namespace App\Http\Controllers\Web\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function location()
    {
        $lat = Setting::where('key', 'school_lat')->first()?->value;
        $long = Setting::where('key', 'school_long')->first()?->value;
        $radius = Setting::where('key', 'school_radius')->first()?->value;
        return view('admin.locations.location', compact('lat', 'long', 'radius'));
    }

    public function updateLocation(Request $request)
    {
        Setting::updateOrCreate(['key' => 'school_lat'], ['value' => $request->lat]);
        Setting::updateOrCreate(['key' => 'school_long'], ['value' => $request->long]);
        Setting::updateOrCreate(['key' => 'school_radius'], ['value' => $request->radius]);
        return redirect()->route('admin.location')->with('success', 'Location settings updated successfully!');
    }
}
