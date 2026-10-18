<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Credit;
use App\Models\Aids;
use App\Models\Undeposited;
use App\Models\ReceivePayment;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function AllCategory(){
        $categories = Category::all();

        return view('admin.pages.category.all_category', compact('categories'));
    }

    public function AddCategory(){
        return view('admin.pages.category.add_category');
    }

    public function StoreCategory(Request $request){

        Category::create([
            'name' => $request->name,
            'slug' => strtolower(str_replace(' ', '-', $request->name)),
            'description' => $request->description,
            'account_type' => $request->account_type,
            'is_monthly_fee' => $request->boolean('is_monthly_fee'),
        ]);

          $notification = array(
            'message' => 'موفقانه اضافه شد',
            'alert-type' => 'success'
        );

        return redirect()->route('all.category')->with($notification);
    }

    public function EditCategory($id){
        $category = Category::findOrFail($id);

        return view('admin.pages.category.edit_category', compact('category'));
    }

    public function UpdateCategory(Request $request){
        $category_id = $request->id;

        Category::findOrFail($category_id)->update([
            'name' => $request->name,
            'slug' => strtolower(str_replace(' ', '-', $request->name)),
            'description' => $request->description,
            'balance' => $request->balance,
            'account_type' => $request->account_type,
            'is_monthly_fee' => $request->boolean('is_monthly_fee'),
        ]);

        $notification = array(
            'message' => 'موفقانه به روزرسانی شد',
            'alert-type' => 'success'
        );

        return redirect()->route('all.category')->with($notification);
    }

    public function DeleteCategory($id){
        Category::findOrFail($id)->delete();

        $notification = array(
            'message' => 'موفقانه حذف شد',
            'alert-type' => 'success'
        );

        return redirect()->route('all.category')->with($notification);
    }

    public function ReportsCategory($id){
        $category = Category::findOrFail($id);

        $incomes = Income::where('category_id', $id)->orWhereHas('undeposited', function ($query) use ($id) {
            $query->where('status', 'transferred')
                ->where('target_account_id', $id);
        })->with('category')->get();

        $expenses = Expense::with(['category', 'sourceAccount'])->where(function ($query) use ($id) {
            $query->where('category_id', $id)
                ->orWhere('source_account_id', $id);
        })->get();

        $credits = Credit::with(['user','category','sourceAccount','payments'])->where('source_account_id', $id)->get();

        // $receivePayments = ReceivePayment::with(['users', 'category'])->where('category_id', $id)->get();

        $aids = Aids::with(['user', 'category', 'sourceAccount'])->where(function ($query) use ($id) {
            $query->where('category_id', $id)
                ->orWhere('source_account_id', $id);
        })->get();

        $undepositedTransfers = Undeposited::with([
            'income.category',
            'targetAccount',
        ])
        ->where('status', 'transferred')
        ->get();

        return view('admin.pages.category.category_reports', compact(
            'category',
            'incomes',
            'expenses',
            'credits',
            'undepositedTransfers',
            // 'receivePayments',
            'aids'
        ));
    }
}
