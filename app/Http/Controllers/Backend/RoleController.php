<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function AllPermission () {

    $permissions = Permission::all();

    return view('admin.pages.permission.all_permission' , compact('permissions'));

    }

     public function AddPermission () {


    return view('admin.pages.permission.add_permission');

    }

    public function StorePermission(Request $request) {
        $request->validate([
            'name' => [
                'required',
                \Illuminate\Validation\Rule::in(array_keys(config('shura_permissions.permissions'))),
                \Illuminate\Validation\Rule::unique('permissions', 'name')->where('guard_name', 'web'),
            ],
            'group_name' => ['required', \Illuminate\Validation\Rule::in(array_keys(config('shura_permissions.groups')))],
        ], [
            'name.required' => 'لطفاً نام مجوز را انتخاب کنید.',
            'name.unique' => 'این مجوز قبلاً اضافه شده است.',
            'group_name.required' => 'لطفاً گروپ را انتخاب کنید.',
        ]);

        Permission::create([
            'name' => $request->name,
            'group_name' => $request->group_name,
        ]);
         $notification = [

            'message' => 'مجوز موفقانه اضافه شد',

            'alert-type' => 'success'

        ];

        return redirect()->route('all.permission')->with($notification);
    }

     public function EditPermission(int $id){
        $permissions = Permission::find($id);
        return view('admin.pages.permission.edit_permission',compact('permissions'));

     }
     // End Method 

     public function UpdatePermission(Request $request){
        $per_id = $request->id;

        $request->validate([
            'id' => 'required|exists:permissions,id',
            'name' => [
                'required',
                \Illuminate\Validation\Rule::in(array_keys(config('shura_permissions.permissions'))),
                \Illuminate\Validation\Rule::unique('permissions', 'name')->where('guard_name', 'web')->ignore($per_id),
            ],
            'group_name' => ['required', \Illuminate\Validation\Rule::in(array_keys(config('shura_permissions.groups')))],
        ], [
            'name.unique' => 'این مجوز قبلاً اضافه شده است.',
        ]);

        Permission::find($per_id)->update([
            'name' => $request->name,
            'group_name' => $request->group_name,
        ]);

        $notification = array(
            'message' => 'مجوز موفقانه ویرایش شد',
            'alert-type' => 'success'
         ); 
         return redirect()->route('all.permission')->with($notification); 
    }
     // End Method 

     public function DeletePermission(int $id){
        Permission::find($id)->delete();

        $notification = array(
            'message' => 'Permission Deleted Successfully',
            'alert-type' => 'success'
         ); 
         return redirect()->back()->with($notification); 

     }
      // End Method 

       public function AllRoles(){
        $roles = Role::all();
        return view('admin.pages.role.all_role',compact('roles')); 
    }
    // End Method 

    public function AddRoles(){ 
        return view('admin.pages.role.add_role'); 
    }
    // End Method 

    public function StoreRoles(Request $request){

        Role::create([
            'name' => $request->name, 
        ]);

        $notification = array(
            'message' => 'Role Inserted Successfully',
            'alert-type' => 'success'
         ); 
         return redirect()->route('all.roles')->with($notification); 
    }
     // End Method 

      public function EditRoles($id){
        $roles = Role::find($id);
        return view('admin.pages.role.edit_role',compact('roles'));

     }
     // End Method 

     public function UpdateRoles(Request $request){
        $role_id = $request->id;

        Role::find($role_id)->update([
            'name' => $request->name, 
        ]);

        $notification = array(
            'message' => 'Role Updated Successfully',
            'alert-type' => 'success'
         ); 
         return redirect()->route('all.roles')->with($notification); 
    }
     // End Method 

       public function DeleteRoles($id){
        Role::find($id)->delete();

        $notification = array(
            'message' => 'Role Deleted Successfully',
            'alert-type' => 'success'
         ); 
         return redirect()->back()->with($notification); 

     }
      // End Method 

       ///////////////// Add Role Permission All Methods /////////

    public function AddRolesPermission(){
        $roles = Role::all();
        $permissions = Permission::all();
        $permission_groups = User::getpermissionGroups();
        return view('admin.pages.rolesetup.add_roles_permission',compact('roles','permissions','permission_groups'));

    }
     // End Method

      public function RolePermissionStore(Request $request){

        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission' => 'nullable|array',
            'permission.*' => 'exists:permissions,id',
        ]);

        $role = Role::findOrFail($request->role_id);
        $permissionNames = Permission::whereIn('id', $request->permission ?? [])
            ->pluck('name')
            ->toArray();

        $role->syncPermissions($permissionNames);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $notification = array(
            'message' => 'Role Permission Added Successfully',
            'alert-type' => 'success'
         ); 
         return redirect()->route('all.roles.permission')->with($notification); 

     }
      // End Method

       public function AllRolesPermission(){
        $roles = Role::all();
        return view('admin.pages.rolesetup.all_roles_permission',compact('roles'));
      }
      // End Method

      public function AdminEditRoles($id){
        $role = Role::find($id);
        $permissions = Permission::all();
        $permission_groups = User::getpermissionGroups();
        return view('admin.pages.rolesetup.edit_roles_permission',compact('role','permissions','permission_groups'));

      }
      // End Method

       public function AdminRolesUpdate(Request $request, $id){
        $role = Role::findOrFail($id);
        $permissionNames = Permission::whereIn('id', $request->permission ?? [])
            ->pluck('name')
            ->toArray();

        $role->syncPermissions($permissionNames);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $notification = array(
            'message' => 'Role Permission Updated Successfully',
            'alert-type' => 'success'
         ); 
         return redirect()->route('all.roles.permission')->with($notification); 

    }
       // End Method

        public function AdminDeleteRoles($id){

        $role = Role::find($id);
        if (!is_null($role)) {
           $role->delete();
        }

       $notification = array(
            'message' => 'Role Permission Deleted Successfully',
            'alert-type' => 'success'
         ); 
         return redirect()->back()->with($notification); 
    }
    // End Method


     ////////////// Admin User All Method ////////////

    public function AllAdmin(){
        $alladmin = User::where('role','admin')->latest()->get();
        return view('admin..pages.admin.all_admin',compact('alladmin')); 
    }
    // End Method

      public function AddAdmin(){
        $roles = Role::all();
        return view('admin..pages.admin.add_admin',compact('roles')); 
    }
    // End Method

     public function StoreAdmin(Request $request){

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = 'admin';
        $user->save();

        if ($request->roles) {
            $role = Role::where('id',$request->roles)->where('guard_name','web')->first();
            if ($role) {
                $user->assignRole($role->name);
            }
        }

        $notification = array(
            'message' => 'New Admin Inserted Successfully',
            'alert-type' => 'success'
         ); 
         return redirect()->route('all.admin')->with($notification); 

    }
     // End Method

      public function EditAdmin($id){
        $admin = User::find($id);
        $roles = Role::all();
        return view('admin.pages.admin.edit_admin',compact('admin','roles')); 
    }
    // End Method

    public function UpdateAdmin(Request $request,$id){

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email; 
        $user->role = 'admin';
        $user->save();
        
        $user->roles()->detach();

        if ($request->roles) {
            $role = Role::where('id',$request->roles)->where('guard_name','web')->first();
            if ($role) {
                $user->assignRole($role->name);
            }
        }

        $notification = array(
            'message' => 'New Admin Updated Successfully',
            'alert-type' => 'success'
         ); 
         return redirect()->route('all.admin')->with($notification); 

    }
     // End Method

    public function DeleteAdmin($id){

        $admin = User::find($id);
        if (!is_null($admin)) {
            $admin->delete();
        }

        $notification = array(
            'message' => ' Admin Deleted Successfully',
            'alert-type' => 'success'
         ); 
         return redirect()->back()->with($notification); 

    }
     // End Method












}
