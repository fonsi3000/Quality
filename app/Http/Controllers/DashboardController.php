<?php

namespace App\Http\Controllers;

use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $activeUsersCount = User::count(); // O si tienes un campo active: User::where('active', true)->count();
        
        return view('dashboard', compact('activeUsersCount'));
    }
}