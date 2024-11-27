<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DocumentRequest;

class DashboardController extends Controller 
{
    public function index()
    {
        $activeUsersCount = User::count();
        
        $activeTasks = DocumentRequest::whereNotIn('status', [
            DocumentRequest::STATUS_PUBLICADO,
            DocumentRequest::STATUS_RECHAZADO
        ])->count();
        
        $publishedDocuments = DocumentRequest::where('status', 
            DocumentRequest::STATUS_PUBLICADO
        )->count();

        return view('dashboard', compact(
            'activeUsersCount',
            'activeTasks',
            'publishedDocuments'
        ));
    }
}