<?php

namespace App\Http\Controllers\PartnerHandbook;

use App\Resource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function get(){
        $resources=Resource::all();
        // dd($resources);
        return view('partner-handbook.index',compact('resources',$resources));
    }
}