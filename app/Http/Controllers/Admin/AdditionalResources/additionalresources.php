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
        // Validate that a file is uploaded and check the file extension
        $this->validate($request, [
            'file' => 'required|file|mimes:pdf,doc,docx,csv,xml|max:10240'  // max:10MB (adjust as needed)
        ]);

        $file = $request->file('file');

        // Generate a new name for the file using a random string and original extension
        $file_name = $file->getClientOriginalName();
        $new_name = pathinfo($file_name, PATHINFO_FILENAME);

        // Move the file to the public 'uploads' directory
        $file->move(public_path('uploads'), $new_name);

        // Store file data in the database
        $form_data = array(
            'file' => $new_name
        );

        // Store the file info in the database (replace `ar::create()` with your model)
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
        return redirect()->route('admin.additionalresource.create')->with('success','Resource updated successfully');
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
