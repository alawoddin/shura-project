<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\FamilyMembers;
use App\Models\User;
use Illuminate\Http\Request;

class FamilyController extends Controller
{

    public function AllUsersFamily($id){

        $user = User::findOrFail($id);

        $familyMembers = FamilyMembers::where('user_id', $id)->latest()->get();

        return view('admin.pages.family.all_users_family', compact('familyMembers','user'));
    }

    public function AddUsersFamily($id){

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

        return redirect()->route('all.users.family',$request->user_id)->with($notification);

    }

    public function EditUsersFamily($id){

        $familyMember = FamilyMembers::findOrFail($id);
        return view('admin.pages.family.edit_users_family', compact('familyMember'));
    }

    public function UpdateUsersFamily(Request $request)  {
        $familyMember = FamilyMembers::findOrFail($request->id);
        $familyMember->update([
            'name' => implode(',', $request->family_members),
        ]);

          $notification = array(
            'message' => 'کاربر موفقانه اپدیت شد',
            'alert-type' => 'success'
        );

        return redirect()->route('all.users.family',$familyMember->user_id)->with($notification);

    }

    public function DeleteUsersFamily($id){

        $familyMember = FamilyMembers::findOrFail($id);

        $user_id = $familyMember->user_id;

        $familyMember->delete();

         $notification = array(
            'message' => 'کاربر موفقانه حذف شد',
            'alert-type' => 'success'
        );

        return redirect()->route('all.users.family',$user_id)->with($notification);
    }
}
