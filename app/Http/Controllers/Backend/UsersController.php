<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function AllUsers(){
        $users = User::latest()->get();
        return view('admin.pages.users.all_users', compact('users'));
    }

    public function AddUser(){
        return view('admin.pages.users.add_user');
    }
}
