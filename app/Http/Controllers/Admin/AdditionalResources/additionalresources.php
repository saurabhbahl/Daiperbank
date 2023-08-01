<?php

namespace App\Http\Controllers\Admin\AdditionalResources;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\additionalrs as ar;
 
class additionalresources extends Controller
{
    public function index(){
        return view ('admin.additionalresources.index');
    }
    
    public function create(Request $request)
    {
        $this->validate($request, [
           'file' => 'required'  
        ]);
  
        $file = $request->file('file');
        // dd($file);

        $new_name = rand() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads'), $new_name);
        $form_data = array(
            'file' => $new_name
        );

        ar::create($form_data);

        return redirect()->back()->with('success', 'Resource uploaded successfully.');
    }

    public function show(ar $resource)
    {
        $resources = ar::all();
        return view('admin.additionalresources.index',compact('resources'));
    }

    public function edit($id)
    {
        $resource = ar::find($id);
        // dd($resource);
        return view('admin.additionalresources.edit',compact('resource'));
    }

    public function update(Request $request, $id)
    {
        $resource=ar::find($id);
        $this->validate($request, [
            'file' => 'required'  
         ]);

        $destination = 'uploads/'.$resource->file;
        if(File::exists($destination)){
             File::delete($destination);
        }
        $file = $request->file('file');
        $new_name = rand() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads'), $new_name);
         $form_data = array(
             'file' => $new_name
         );

        $resource->update($form_data);
        return redirect()->route('admin.additionalresources.create')->with('success','Resource updated successfully');
    }

    public function destroy(Request $request, $id)
    {
        $resource = ar::find($id);
        $destination = 'uploads/'.$resource->file;
        if(File::exists($destination)){
             File::delete($destination);
        }
        $resource->delete();

        return redirect()->back()->with('success','Resource deleted successfully');
    }
}
