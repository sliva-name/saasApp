<?php

namespace App\Http\Controllers;

use App\Models\Store;

class DashboardController extends Controller
{
    public function index()
    {
        $stores = Store::with(['owner', 'theme', 'domains'])
            ->orderByDesc('created_at')
            ->paginate(10);
        return view('dashboard', compact('stores'));
    }
}
