<?php

namespace App\Http\Controllers\Admin\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NotificationSetting;

class SettingController extends Controller
{
    public function index(){
        $lastRecord = NotificationSetting::latest()->first();
        return view('admin.settings.index',compact('lastRecord',$lastRecord));
    }

    public function store(Request $request){
        $formData = $request->all();
        // dd($formData);
        NotificationSetting::create($formData);
      
        return redirect()->back()->with('success', 'Notification created successfully.');

    }
}