<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\FamilyMembers;
use App\Models\User;
use App\Services\FamilyMemberAccountService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FamilyController extends Controller
{
    public function __construct(
        protected FamilyMemberAccountService $accountService
    ) {}

    public function AllUsersFamily($id)
    {
        $user = User::findOrFail($id);
        $familyMembers = FamilyMembers::with(['user', 'linkedUser'])
            ->where('user_id', $id)
            ->latest()
            ->get();

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

        DB::transaction(function () use ($request) {
            $familyMember = FamilyMembers::create([
                'user_id' => $request->user_id,
                'name' => $request->name,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'marital_status' => $request->marital_status ?? 'single',
                'qualification' => $request->qualification,
                'degree' => $request->degree,
                'note' => $request->note,
            ]);

            if ($familyMember->marital_status === 'married') {
                $this->accountService->createAccountFromFamilyMember($familyMember);
            }
        });

        return redirect()->route('all.users.family', $request->user_id)->with([
            'message' => 'اعضای فامیل موفقانه اضافه شد',
            'alert-type' => 'success',
        ]);
    }

    public function EditUsersFamily($id)
    {
        $familyMember = FamilyMembers::with('linkedUser')->findOrFail($id);

        return view('admin.pages.family.edit_users_family', compact('familyMember'));
    }

    public function UpdateUsersFamily(Request $request)
    {
        $familyMember = FamilyMembers::findOrFail($request->id);
        $wasMarried = $familyMember->marital_status === 'married';

        DB::transaction(function () use ($request, $familyMember, $wasMarried) {
            $familyMember->update([
                'name' => $request->name,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'marital_status' => $request->marital_status ?? 'single',
                'qualification' => $request->qualification,
                'degree' => $request->degree,
                'note' => $request->note,
            ]);

            if ($request->marital_status === 'married' && ! $familyMember->linked_user_id) {
                $this->accountService->createAccountFromFamilyMember($familyMember->fresh());
            }
        });

        $message = 'اعضای فامیل موفقانه اپدیت شد';
        if ($request->marital_status === 'married' && ! $wasMarried && $familyMember->fresh()->linked_user_id) {
            $message = 'عضو فامیل به‌روزرسانی شد و حساب کاربری جداگانه ساخته شد';
        }

        return redirect()->route('all.users.family', $familyMember->user_id)->with([
            'message' => $message,
            'alert-type' => 'success',
        ]);
    }

    public function DeleteUsersFamily($id)
    {
        $familyMember = FamilyMembers::findOrFail($id);
        $user_id = $familyMember->user_id;
        $familyMember->delete();

        return redirect()->route('all.users.family', $user_id)->with([
            'message' => 'عضو فامیل با موفقیت حذف شد',
            'alert-type' => 'success',
        ]);
    }

    public function CreateFamilyMemberAccount($id)
    {
        $familyMember = FamilyMembers::findOrFail($id);

        if ($familyMember->linked_user_id) {
            return redirect()->route('all.users.family', $familyMember->user_id)->with([
                'message' => 'برای این عضو قبلاً حساب کاربری ساخته شده است',
                'alert-type' => 'warning',
            ]);
        }

        DB::transaction(function () use ($familyMember) {
            $familyMember->update(['marital_status' => 'married']);
            $this->accountService->createAccountFromFamilyMember($familyMember->fresh());
        });

        return redirect()->route('all.users.family', $familyMember->user_id)->with([
            'message' => 'حساب کاربری جداگانه با موفقیت ساخته شد',
            'alert-type' => 'success',
        ]);
    }

    private function calculateAge(?string $birthDate): ?int
    {
        if (empty($birthDate)) {
            return null;
        }

        return Carbon::parse($birthDate)->age;
    }
}
