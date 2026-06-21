<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
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
            'message' => 'کاربر موفقانه اضافه شد',
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
            'account_type' => $request->account_type,
            'is_monthly_fee' => $request->boolean('is_monthly_fee'),
        ]);

        $notification = array(
            'message' => 'دسته بندی موفقانه به روزرسانی شد',
            'alert-type' => 'success'
        );

        return redirect()->route('all.category')->with($notification);
    }

    public function DeleteCategory($id){
        Category::findOrFail($id)->delete();

        $notification = array(
            'message' => 'دسته بندی موفقانه حذف شد',
            'alert-type' => 'success'
        );

        return redirect()->route('all.category')->with($notification);
    }
}
