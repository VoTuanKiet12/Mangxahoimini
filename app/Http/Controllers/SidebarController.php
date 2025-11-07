<?php

namespace App\Http\Controllers;

use App\Models\KetBan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SidebarController extends Controller
{
    public function index()
    {
        return view('layouts.sidebar-right');
    }
}
