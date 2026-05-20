<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\FamilyMembers;
use App\Models\User;
use Illuminate\Http\Request;

class FamilyController extends Controller
{

    public function AllUsersFamily(){
        
        $familyMembers = FamilyMembers::latest()->get();
        return view('admin.pages.family.all_users_family', compact('familyMembers'));
    }

    public function AddUsersFamily(int $id){

     $user = User::findOrFail($id);
    
        return view('admin.pages.family.add_users_family', compact('user'));
    }

    public function StoreUsersFamily(Request $request)  {
        FamilyMembers::create([
            'user_id' => $request->user_id,
            'name' => implode(',', $request->family_members),
        ]);

          $notification = array(
            'message' => 'کاربر موفقانه اضافه شد',
            'alert-type' => 'success'
        );

        return redirect()->route('all.users.family')->with($notification);

    }
}
