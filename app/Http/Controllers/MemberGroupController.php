<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function listMembers(Request $request, $id){
        $group = Group::find($id);
        
    }
}
