<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Aids;
use Illuminate\Http\Request;

class AidController extends Controller
{
    public function AllAids(){
        $alldata = Aids::with(['user', 'category'])->get();
        return view('admin.pages.aid.all_aids', compact('alldata'));
    }
}
