<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function AllUsers(){
        $users = User::latest()->get();
        return view('admin.pages.users.all_users', compact('users'));
    }

    public function AddUser(){
        return view('admin.pages.users.add_user');
    }

    public function StoreUser(Request $request){
        $photoName = null;
        $documentName = null;
       
        if($request->file('photo')){
            $photo = $request->file('photo');
            $photoName = date('YmdHi').$photo->getClientOriginalName();
            $photo->move(public_path('upload/user_images'), $photoName);
        }

        if($request->file('documents')){
            $document = $request->file('documents');
            $documentName = date('YmdHi').$document->getClientOriginalName();
            $document->move(public_path('upload/user_documents'), $documentName);
        }

        User::create([
            'name' => $request->name,
            'father_name' => $request->father_name,
            'grandfather_name' => $request->grandfather_name,
            'lastname' => $request->lastname,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'marital_status' => $request->marital_status,
            'permanent_address' => $request->permanent_address,
            'current_address' => $request->current_address,
            'education_level' => $request->education_level,
            'job' => $request->job,
            'work_place' => $request->work_place,
            'phone' => $request->phone,
            'economic_status' => $request->economic_status,
            'family_members' => $request->family_members,
            'register_date' => $request->register_date,
            'status' => $request->status,
            'member_type' => $request->member_type,
            'monthly_fee' => $request->monthly_fee,
            'ethnic_branch' => $request->ethnic_branch,
            'representative_name' => $request->representative_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'photo' => $photoName,
            'documents' => $documentName,
        ]);

        $notification = array(
            'message' => 'کاربر موفقانه اضافه شد',
            'alert-type' => 'success'
        );

        return redirect()->route('all.users')->with($notification);
    }

    public function EditUsers($id){
        $users = User::findOrFail($id);
        return view('admin.pages.users.edit_users', compact('users'));
    }

    public function UpdateUsers(Request $request){
        $user = User::findOrFail($request->id);
        if($request->file('photo')){
            @unlink(public_path('upload/user_images/'.$user->photo));
            $photo = $request->file('photo');
            $photoName = date('YmdHi').$photo->getClientOriginalName();
            $photo->move(public_path('upload/user_images'), $photoName);
            $user->photo = $photoName;
        }

        if($request->file('documents')){
            @unlink(public_path('upload/user_documents/'.$user->documents));
            $document = $request->file('documents');
            $documentName = date('YmdHi').$document->getClientOriginalName();
            $document->move(public_path('upload/user_documents'), $documentName);
            $user->documents = $documentName;
        }

        $user->update([
            'name' => $request->name,
            'father_name' => $request->father_name,
            'grandfather_name' => $request->grandfather_name,
            'lastname' => $request->lastname,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'marital_status' => $request->marital_status,
            'permanent_address' => $request->permanent_address,
            'current_address' => $request->current_address,
            'education_level' => $request->education_level,
            'job' => $request->job,
            'work_place' => $request->work_place,
            'phone' => $request->phone,
            'economic_status' => $request->economic_status,
            'family_members' => $request->family_members,
            'register_date' => $request->register_date,
            'status' => $request->status,
            'member_type' => $request->member_type,
            'monthly_fee' => $request->monthly_fee,
            'ethnic_branch' => $request->ethnic_branch,
            'representative_name' => $request->representative_name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        if($request->password){
            $user->update([
                'password' => Hash::make($request->password)
            ]);
        }

        $notification = array(
            'message' => 'کاربر موفقانه ویرایش شد',
            'alert-type' => 'success'
        );

        return redirect()->route('all.users')->with($notification);
    }

    public function DeleteUser($id){
        $user = User::findOrFail($id);

        if($user->photo){
            @unlink(public_path('upload/user_images/'.$user->photo));
        }
        if($user->documents){
            @unlink(public_path('upload/user_documents/'.$user->documents));
        }
        $user->delete();

        $notification = array(
            'message' => 'کاربر موفقانه حذف شد',
            'alert-type' => 'success'
        );
        return redirect()->route('all.users')->with($notification);
    }
}
