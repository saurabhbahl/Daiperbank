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
        
        // Check if 'disable' is present in the form data
        if (isset($formData['disable']) && $formData['disable'] === 'on') {
            $formData['disable'] = true;
        } else {
            // If 'disable' is not set, or it's unchecked, set it to false
            $formData['disable'] = false;
        }

        if(isset($formData['id'])){
            $notification = NotificationSetting::findorFail($formData['id']);
            $notification->update($formData);
        }
        else{  
            NotificationSetting::create($formData);
        }
        // dd($formData);
      
        return redirect()->back()->with('success', 'Notification created successfully.');

    }
}