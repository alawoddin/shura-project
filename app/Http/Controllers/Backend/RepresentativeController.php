<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\EthnicBranch;
use App\Models\Representative;
use Illuminate\Http\Request;

class RepresentativeController extends Controller
{
    public function AllRepresentatives()
    {
        $allRepresentatives = Representative::with('ethnicBranch')->latest()->get();

        return view('admin.pages.representatives.all_representatives', compact('allRepresentatives'));
    }

    public function AddRepresentative()
    {
        $ethnicBranches = EthnicBranch::orderBy('name')->get();

        return view('admin.pages.representatives.add_representative', compact('ethnicBranches'));
    }

    public function StoreRepresentative(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ethnic_branch_id' => 'required|exists:ethnic_branches,id',
        ]);

        Representative::create([
            'name' => $request->name,
            'ethnic_branch_id' => $request->ethnic_branch_id,
        ]);

        return redirect()->route('all.representatives')->with([
            'message' => 'نماینده با موفقیت اضافه شد',
            'alert-type' => 'success',
        ]);
    }

    public function EditRepresentative($id)
    {
        $representative = Representative::findOrFail($id);
        $ethnicBranches = EthnicBranch::orderBy('name')->get();

        return view('admin.pages.representatives.edit_representative', compact('representative', 'ethnicBranches'));
    }

    public function UpdateRepresentative(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:representatives,id',
            'name' => 'required|string|max:255',
            'ethnic_branch_id' => 'required|exists:ethnic_branches,id',
        ]);

        Representative::findOrFail($request->id)->update([
            'name' => $request->name,
            'ethnic_branch_id' => $request->ethnic_branch_id,
        ]);

        return redirect()->route('all.representatives')->with([
            'message' => 'نماینده با موفقیت به‌روزرسانی شد',
            'alert-type' => 'success',
        ]);
    }

    public function DeleteRepresentative($id)
    {
        Representative::findOrFail($id)->delete();

        return redirect()->route('all.representatives')->with([
            'message' => 'نماینده با موفقیت حذف شد',
            'alert-type' => 'success',
        ]);
    }

    public function ByEthnicBranch($ethnicBranchId)
    {
        $representatives = Representative::where('ethnic_branch_id', $ethnicBranchId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($representatives);
    }
}
