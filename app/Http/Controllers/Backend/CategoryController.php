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
        ]);

        return redirect()->route('all.category')->with('success', 'Category Inserted Successfully');
    }
}
