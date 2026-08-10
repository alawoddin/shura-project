<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\EthnicBranch;
use Illuminate\Http\Request;

class EthnicBranchController extends Controller
{
    public function AllEthnic(){
        $all_ethnic = EthnicBranch::all();
        return view('admin.pages.ethnics.all_ethnic', compact('all_ethnic'));
    }
}
