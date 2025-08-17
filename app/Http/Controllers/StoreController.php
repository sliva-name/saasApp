<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Store;
use App\Services\StoreCreator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class StoreController extends Controller
{
    public function create()
    {
        $plans = Plan::all();

        return view('stores.create', compact('plans'));
    }

    public function store(Request $request, StoreCreator $creator)
    {
        $request->validate([
            'plan' => 'required|in:Free,Basic,Pro',
            'custom_domain' => 'nullable|string|regex:/^[a-z0-9]+([\-]?[a-z0-9]+)*(\.[a-z]{2,})+$/i|unique:domains,domain|max:50',
            'theme_id' => ['nullable', 'integer', 'exists:themes,id'],
        ]);

        $user = Auth::user();

        $store = $creator->create($user, $request->plan, $request->custom_domain, $request->theme_id);

        return redirect()->route('stores.show', $store->id)->with('success', 'Магазин создан!');
    }
    public function index()
    {
        $stores = Store::with(['owner', 'theme', 'domains'])
            ->orderByDesc('created_at')
            ->paginate(10);
            
        return view('stores.index', compact('stores'));
    }
}
