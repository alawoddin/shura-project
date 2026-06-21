<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\FamilyMembers;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FamilyController extends Controller
{
    public function AllUsersFamily($id)
    {
        $user = User::findOrFail($id);
        $familyMembers = FamilyMembers::where('user_id', $id)->latest()->get();

        return view('admin.pages.family.all_users_family', compact('familyMembers', 'user'));
    }

    public function AddUsersFamily($id)
    {
        $user = User::findOrFail($id);

        return view('admin.pages.family.add_users_family', compact('user'));
    }

    public function StoreUsersFamily(Request $request)
    {
        $count = FamilyMembers::where('user_id', $request->user_id)->count();
        $user = User::findOrFail($request->user_id);

        if ($count >= $user->family_members) {
            return redirect()->back()->with([
                'message' => 'تعداد اعضای فامیل تکمیل شده',
                'alert-type' => 'error',
            ]);
        }

        $age = $this->calculateAge($request->birth_date);

        FamilyMembers::create([
            'user_id' => $request->user_id,
            'name' => $request->name,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'age' => $age ?? $request->age,
            'qualification' => $request->qualification,
            'degree' => $request->degree,
            'note' => $request->note,
        ]);

        $notification = [
            'message' => 'اعضای فامیل موفقانه اضافه شد',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.users.family', $request->user_id)->with($notification);
    }

    public function EditUsersFamily($id)
    {
        $familyMember = FamilyMembers::findOrFail($id);

        return view('admin.pages.family.edit_users_family', compact('familyMember'));
    }

    public function UpdateUsersFamily(Request $request)
    {
        $familyMember = FamilyMembers::findOrFail($request->id);
        $age = $this->calculateAge($request->birth_date);

        $familyMember->update([
            'name' => $request->name,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'age' => $age ?? $request->age,
            'qualification' => $request->qualification,
            'degree' => $request->degree,
            'note' => $request->note,
        ]);

        $notification = [
            'message' => 'اعضای فامیل موفقانه اپدیت شد',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.users.family', $familyMember->user_id)->with($notification);
    }

    public function DeleteUsersFamily($id)
    {
        $familyMember = FamilyMembers::findOrFail($id);
        $user_id = $familyMember->user_id;
        $familyMember->delete();

        $notification = [
            'message' => 'عضو فامیل با موفقیت حذف شد',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.users.family', $user_id)->with($notification);
    }

    private function calculateAge(?string $birthDate): ?int
    {
        if (empty($birthDate)) {
            return null;
        }

        return Carbon::parse($birthDate)->age;
    }
}
