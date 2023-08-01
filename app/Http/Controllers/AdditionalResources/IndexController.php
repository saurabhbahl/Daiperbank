<?php

namespace App\Http\Controllers\AdditionalResources;

use App\additionalrs;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function get(){
        $resources=additionalrs::all();
        // dd($resources);
        return view('additional-resources.index',compact('resources',$resources));
    }
}