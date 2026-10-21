<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\EthnicBranch;
use App\Models\ReceivePayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function AllUsers()
    {
        $users = User::with(['ethnicBranch', 'representativeName'])
            ->where('role', 'user')
            ->latest()
            ->get();

        return view('admin.pages.users.all_users', compact('users'));
    }

    public function AddUser()
    {
        $ethnicBranches = EthnicBranch::orderBy('name')->get();

        return view('admin.pages.users.add_user', compact('ethnicBranches'));
    }

    public function StoreUser(Request $request)
    {
        $photoName = null;
        $documentName = null;

        $nationalId = strtr($request->national_id, [
            '۰'=>'0', '۱'=>'1', '۲'=>'2', '۳'=>'3', '۴'=>'4',
            '۵'=>'5', '۶'=>'6', '۷'=>'7', '۸'=>'8', '۹'=>'9',
        ]);

        $phone = strtr($request->phone, [
            '۰'=>'0', '۱'=>'1', '۲'=>'2', '۳'=>'3', '۴'=>'4',
            '۵'=>'5', '۶'=>'6', '۷'=>'7', '۸'=>'8', '۹'=>'9',
        ]);

        if ($request->file('photo')) {
            $photo = $request->file('photo');
            $photoName = date('YmdHi').$photo->getClientOriginalName();
            $photo->move(public_path('upload/user_images'), $photoName);
        }

        if ($request->file('documents')) {
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
            'national_id' => $nationalId,
            'birth_date' => $request->birth_date,
            'marital_status' => $request->marital_status,
            'blood_group' => $request->blood_group,
            'permanent_address' => $request->permanent_address,
            'current_address' => $request->current_address,
            'education_level' => $request->education_level,
            'job' => $request->job,
            'work_place' => $request->work_place,
            'phone' => $phone,
            'economic_status' => $request->economic_status,
            'family_members' => $request->family_members,
            'register_date' => $request->register_date,
            'status' => $request->status,
            'member_type' => $request->member_type,
            'monthly_fee' => $request->monthly_fee,
            'ethnic_branch_id' => $request->ethnic_branch_id,
            'representative_id' => $request->representative_id,
            'has_account' => $request->has('has_account'),
            'email' => $request->has('has_account') ? $request->email : null,
            'password' => $request->has('has_account')? Hash::make($request->password): null,
            'role' => $request->role,
            'photo' => $photoName,
            'documents' => $documentName,
        ]);

        return redirect()->route('all.users')->with([
            'message' => 'کاربر موفقانه اضافه شد',
            'alert-type' => 'success',
        ]);
    }

    public function EditUsers($id)
    {
        $users = User::findOrFail($id);
        $ethnicBranches = EthnicBranch::orderBy('name')->get();

        return view('admin.pages.users.edit_users', compact('users', 'ethnicBranches'));
    }

    public function UpdateUsers(Request $request)
    {
        $user = User::findOrFail($request->id);

        $nationalId = strtr($request->national_id, [
            '۰'=>'0', '۱'=>'1', '۲'=>'2', '۳'=>'3', '۴'=>'4',
            '۵'=>'5', '۶'=>'6', '۷'=>'7', '۸'=>'8', '۹'=>'9',
        ]);

        $phone = strtr($request->phone, [
            '۰'=>'0', '۱'=>'1', '۲'=>'2', '۳'=>'3', '۴'=>'4',
            '۵'=>'5', '۶'=>'6', '۷'=>'7', '۸'=>'8', '۹'=>'9',
        ]);

        if ($request->file('photo')) {
            @unlink(public_path('upload/user_images/'.$user->photo));
            $photo = $request->file('photo');
            $photoName = date('YmdHi').$photo->getClientOriginalName();
            $photo->move(public_path('upload/user_images'), $photoName);
            $user->photo = $photoName;
        }

        if ($request->file('documents')) {
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
            'national_id' => $nationalId,
            'birth_date' => $request->birth_date,
            'marital_status' => $request->marital_status,
            'blood_group' => $request->blood_group,
            'permanent_address' => $request->permanent_address,
            'current_address' => $request->current_address,
            'education_level' => $request->education_level,
            'job' => $request->job,
            'work_place' => $request->work_place,
            'phone' => $phone,
            'economic_status' => $request->economic_status,
            'family_members' => $request->family_members,
            'register_date' => $request->register_date,
            'status' => $request->status,
            'member_type' => $request->member_type,
            'monthly_fee' => $request->monthly_fee,
            'ethnic_branch_id' => $request->ethnic_branch_id,
            'representative_id' => $request->representative_id,
            'has_account' => $request->has('has_account'),
            'email' => $request->has('has_account') ? $request->email : null,
            'role' => $request->role,
        ]);

        if ($request->password) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('all.users')->with([
            'message' => 'کاربر موفقانه ویرایش شد',
            'alert-type' => 'success',
        ]);
    }

    public function DeleteUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->photo) {
            @unlink(public_path('upload/user_images/'.$user->photo));
        }
        if ($user->documents) {
            @unlink(public_path('upload/user_documents/'.$user->documents));
        }
        $user->delete();

        return redirect()->route('all.users')->with([
            'message' => 'کاربر موفقانه حذف شد',
            'alert-type' => 'success',
        ]);
    }

    public function UsersDetails(int $id)
    {
        $users = User::with(['ethnicBranch', 'representativeName'])->findOrFail($id);

        return view('admin.pages.users.users_details', compact('users'));
    }

    public function FeeReport($id){
        $user = User::findOrFail($id);

        $start = Carbon::parse($user->register_date ?? $user->created_at)
            ->startOfMonth();

        $currentYear = now()->year;
        $year = (int) request('year', $currentYear);

        $startYear = $start->year;
        abort_if($year < $startYear || $year > $currentYear, 404);

        $from = max($start->copy(), Carbon::create($year, 1, 1));
        $to = Carbon::create($year, 12, 1);

        if ($year === $currentYear) {
            $to = now()->startOfMonth();
        }

        $payments = ReceivePayment::where('user_id', $user->id)
            ->where('transaction_type', 'credit')
            ->whereBetween('month_of', [
                $from->format('Y-m'),
                $to->format('Y-m')
            ])
            ->selectRaw('month_of, SUM(amount) as paid')
            ->groupBy('month_of')
            ->pluck('paid', 'month_of');

        $months = [];

        for ($date = $from->copy()->startOfMonth();
            $date <= $to;
            $date->addMonth()) {
            $key = $date->format('Y-m');
            $expected = (float) $user->monthly_fee;
            $paid = (float) ($payments[$key] ?? 0);

            $months[] = [
                'month' => $key,
                'expected' => $expected,
                'paid' => $paid,
                'remaining' => max(0, $expected - $paid),
                'status' => $paid >= $expected
                    ? 'پرداخت کامل'
                    : ($paid > 0 ? 'پرداخت ناقص' : 'پرداخت نشده'),
            ];
        }

        return view('admin.pages.users.fee_report', compact(
            'user', 'months', 'year', 'startYear', 'currentYear'
        ));
    }
}
