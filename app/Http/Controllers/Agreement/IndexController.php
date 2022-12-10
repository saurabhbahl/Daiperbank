<?php

namespace App\Http\Controllers\Agreement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Agreement;

class IndexController extends Controller
{
    public function get(){
        $userId = Auth::id();
        $agreements = Agreement::where("agency_id", $userId)->get();
        return view('agreement.index',compact('agreements',$agreements));
    }
}
