<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\FinancialService;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct(
        protected FinancialService $financialService
    ) {}

       public function ClientLogout(Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $notification = array(
        'message' => 'Client Logout Successfully',
        'alert-type' => 'success'
     );
        return redirect('/login')->with($notification);
    }
    // End Method

    public function UserDashboard()
    {
        $user = User::with([
            'familyMembers',
            'receivePayments.category',
            'credits.category',
            'aids.category',
            'financialReports',
        ])->findOrFail(Auth::id());

        $feeDebt = $this->financialService->getMemberFeeRemaining($user);

        $stats = [
            'total_payments' => $user->receivePayments
                ->where('transaction_type', 'credit')
                ->sum('amount') - $user->receivePayments
                ->where('transaction_type', 'debit')
                ->sum('amount'),
            'total_credits' => $user->credits->sum('amount'),
            'remaining_credits' => $user->credits->where('status', 'active')->sum('remaining_amount'),
            'total_aid' => $user->aids->sum('amount'),
            'current_balance' => $user->financialReports->sortByDesc('date')->sortByDesc('id')->first()?->balance ?? 0,
            'family_count' => $user->familyMembers->count(),
            'fee_unpaid_months' => $feeDebt['unpaid_months_count'],
            'fee_remaining_amount' => $feeDebt['remaining_amount'],
            'fee_debt_message' => $feeDebt['debt_message'],
            'fee_unpaid_list' => $feeDebt['months'],
        ];

        return view('client.client_index', compact('user', 'stats'));
    }

    public function UserProfile(){
      $id = Auth::user()->id;
      $profileData = User::find($id);
      return view('client.client_profile',compact('profileData'));

   }
   //End Method 

    public function UserProfileStore(Request $request){
        $id = Auth::user()->id;
        $data = User::find($id);

        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->current_address = $request->address;

        $oldPhotoPath = $data->photo;

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');     
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('upload/user_images'),$filename);
            $data->photo = $filename;

            if ($oldPhotoPath && $oldPhotoPath !== $filename) {
            $this->deleteOldImage($oldPhotoPath);
            } 
        }

        $data->save();

        $notification = array(
            'message' => 'User Profile Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
   }
   //End Method 

    private function deleteOldImage(string $oldPhotoPath) : void {
        $fullPath = public_path('upload/user_images/'.$oldPhotoPath);
        if (file_exists($fullPath)) {
        unlink($fullPath);
        }
    }
    //End private Method 

    public function UserChangePassword(){
        return view('client.client_change_password');
    }
    //End Method 

    public function UserPasswordUpdate(Request $request){

        $user = Auth::user(); 
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed'
        ]);

        if (!Hash::check($request->old_password, $user->password)) {
        
            $notification = array(
            'message' => 'Old Password Does not Match!',
            'alert-type' => 'error'
        );

        return back()->with($notification);
        }

        User::whereId($user->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        Auth::logout();

        $notification = array(
            'message' => 'Password Updated Successfully',
            'alert-type' => 'Success'
        );

        return redirect()->route('login')->with($notification); 
    }
    //End Method 


}
