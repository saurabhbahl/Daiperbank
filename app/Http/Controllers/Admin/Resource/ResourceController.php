<?php

namespace App\Http\Controllers\Admin\Resource;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Resource;

class ResourceController extends Controller
{
    public function index(){
        return view ('admin.resource.index');
    }
    
    public function create(Request $request)
    {
        $this->validate($request, [
           'file' => 'required|file|mimes:pdf,doc,docx,csv,xml|max:10240'
        ]);
  
        $file = $request->file('file');

        $file_name = $file->getClientOriginalName();
        $new_name = pathinfo($file_name, PATHINFO_FILENAME);

        $file->move(public_path('uploads'), $new_name);
        $form_data = array(
            'file' => $new_name
        );

        Resource::create($form_data);

        return redirect()->back()->with('success', 'Resource uploaded successfully.');
    }

    public function show(Resource $resource)
    {
        $resources = Resource::all();
        return view('admin.resource.index',compact('resources'));
    }

    public function edit($id)
    {
        $resource = Resource::find($id);
        // dd($resource);
        return view('admin.resource.edit',compact('resource'));
    }

    public function update(Request $request, $id)
    {
        $resource=Resource::find($id);
        $this->validate($request, [
            'file' => 'required|file|mimes:pdf,doc,docx,csv,xml|max:10240', 
         ]);

        $destination = 'uploads/'.$resource->file;
        if(File::exists($destination)){
             File::delete($destination);
        }
        $file = $request->file('file');
        $file_name = $file->getClientOriginalName();
        $new_name = pathinfo($file_name, PATHINFO_FILENAME);
        $file->move(public_path('uploads'), $new_name);
         $form_data = array(
             'file' => $new_name
         );

        $resource->update($form_data);
        return redirect()->route('admin.resource.create')->with('success','Resource updated successfully');
    }

    public function destroy(Request $request, $id)
    {
        $resource = Resource::find($id);
        $destination = 'uploads/'.$resource->file;
        if(File::exists($destination)){
             File::delete($destination);
        }
        $resource->delete();

        return redirect()->back()->with('success','Resource deleted successfully');
    }
}