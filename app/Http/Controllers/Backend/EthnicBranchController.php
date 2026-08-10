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
}
