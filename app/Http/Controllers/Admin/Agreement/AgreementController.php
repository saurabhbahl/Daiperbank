<?php

namespace App\Http\Controllers\Admin\Agreement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Agency;
use App\Agreement;
use DB;

class AgreementController extends Controller
{
     public function index(Request $request){
          if($request->ajax()){
          $agencies=Agency::where('name','LIKE',$request->name.'%')->get();
          $output='';
          if (count($agencies)>0) {
               $output.='<select name="agency_id">';
                  foreach ($agencies as $agency) {
                       $output.='<option value='.$agency->id.'>'.$agency->name.'</option>';
                  }
               $output.='</option>';
          } else {
              $output.='<option class="list-group-item">No Data Found</option>';
          }
          return $output;
          
      }
      return view('admin.agreement.index');
     }

     public function create(Request $request){
          $this->validate($request, [
               'file' => 'required' ,
               'agency_id' => 'required' ,
          ]);

            $file = $request->file('file');
            // dd($file);
    
            $new_name = rand() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/agreements'), $new_name);
            $form_data = array(
                'file' => $new_name,
                'agency_id' => $request->agency_id 
            );
    
            Agreement::create($form_data);
    
            return redirect()->back()->with('success', 'Agreement uploaded successfully.');
     }

     public function show(Request $request)
    {
        if($request->ajax()){
            $agencies=Agency::where('name','LIKE',$request->name.'%')->get();
            $output='';
            if (count($agencies)>0) {
                 $output.='<select name="agency_id">';
                    foreach ($agencies as $agency) {
                         $output.='<option value='.$agency->id.'>'.$agency->name.'</option>';
                    }
                 $output.='</option>';
            } else {
                $output.='<option class="list-group-item">No Data Found</option>';
            }
            return $output;
            
        }
     $agreements = DB::table('agency')
        ->join('agreements', 'agency.id', '=', 'agreements.agency_id')
        ->select('agency.name', 'agreements.id','agreements.file','agreements.created_at','agreements.updated_at')->get();
        return view('admin.agreement.index',compact('agreements'));
    }
    public function edit($id)
    {
        $agreement = Agreement::find($id);
     
        return view('admin.agreement.edit',compact('agreement'));
    }
    public function update(Request $request, $id)
    {
        $agreement=Agreement::find($id);
        $this->validate($request, [
            'file' => 'required'  
         ]);

        $destination = 'uploads/agreements'.$agreement->file;
        if(File::exists($destination)){
             File::delete($destination);
        }
        $file = $request->file('file');
        $new_name = rand() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads'), $new_name);
         $form_data = array(
             'file' => $new_name
         );

        $agreement->update($form_data);
        return redirect()->route('admin.agreement.create')->with('success','Agreement updated successfully');
    }
    public function destroy(Request $request, $id)
    {
        $agreement = Agreement::find($id);
        $destination = 'uploads/agreements'.$agreement->file;
        if(File::exists($destination)){
             File::delete($destination);
        }
        $agreement->delete();

        return redirect()->back()->with('success','Resource deleted successfully');
    }
}
