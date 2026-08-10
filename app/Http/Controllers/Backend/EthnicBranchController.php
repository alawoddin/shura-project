<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\EthnicBranch;
use Illuminate\Http\Request;

class EthnicBranchController extends Controller
{
    public function AllEthnic(){
        $all_ethnic = EthnicBranch::all();
        return view('admin.pages.ethnics.all_ethnic', compact('all_ethnic'));
    }

    public function AddEthnic(){
        return view('admin.pages.ethnics.add_ethnic');
    }

    public function StoreEthnic(Request $request){

        EthnicBranch::create([
            'name' => $request->name,
        ]);

          $notification = array(
            'message' => 'شاخه قومی موفقانه اضافه شد',
            'alert-type' => 'success'
        );

        return redirect()->route('all.ethnic')->with($notification);
    }

    public function EditEthnic($id){
        $ethnic = EthnicBranch::findOrFail($id);

        return view('admin.pages.ethnics.edit_ethnic', compact('ethnic'));
    }

    public function UpdateEthnic(Request $request){
        $ethnic_id = $request->id;

        EthnicBranch::findOrFail($ethnic_id)->update([
            'name' => $request->name,
        ]);

        $notification = array(
            'message' => 'شاخه قومی موفقانه به روزرسانی شد',
            'alert-type' => 'success'
        );

        return redirect()->route('all.ethnic')->with($notification);
    }
}
