<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Admin Dashboard',
            'active' => 'dashboard',
            'survey_count' => Survey::count(),
        ];
        return view('admin.dashboard.index', $data);
    }
}
