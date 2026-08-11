<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\KeyPerson;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KeyPersonController extends Controller
{
    public function index()
    {
        $keyPeople = KeyPerson::with('user')->latest()->get();

        return view('admin.pages.key_people.all_key_people', compact('keyPeople'));
    }

    public function create()
    {
        $users = User::where('role', 'user')->orderBy('name')->get();

        return view('admin.pages.key_people.add_key_person', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('key_personnel', 'user_id'),
            ],
            'position' => 'required|string|max:255',
            'note' => 'nullable|string',
        ]);

        KeyPerson::create([
            'user_id' => $request->user_id,
            'position' => $request->position,
            'note' => $request->note,
        ]);

        return redirect()->route('all.key.people')->with([
            'message' => 'فرد کلیدی با موفقیت اضافه شد',
            'alert-type' => 'success',
        ]);
    }

    public function edit($id)
    {
        $keyPerson = KeyPerson::findOrFail($id);
        $users = User::where('role', 'user')->orderBy('name')->get();

        return view('admin.pages.key_people.edit_key_person', compact('keyPerson', 'users'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:key_personnel,id',
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('key_personnel', 'user_id')->ignore($request->id),
            ],
            'position' => 'required|string|max:255',
            'note' => 'nullable|string',
        ]);

        KeyPerson::findOrFail($request->id)->update([
            'user_id' => $request->user_id,
            'position' => $request->position,
            'note' => $request->note,
        ]);

        return redirect()->route('all.key.people')->with([
            'message' => 'فرد کلیدی با موفقیت به‌روزرسانی شد',
            'alert-type' => 'success',
        ]);
    }

    public function destroy($id)
    {
        KeyPerson::findOrFail($id)->delete();

        return redirect()->route('all.key.people')->with([
            'message' => 'فرد کلیدی با موفقیت حذف شد',
            'alert-type' => 'success',
        ]);
    }
}
