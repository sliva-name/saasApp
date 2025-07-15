<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Services\StoreCreator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class StoreController extends Controller
{
    public function create()
    {
        return view('stores.create');
    }

    public function store(Request $request, StoreCreator $creator)
    {
        $request->validate([
            'plan' => 'required|in:free,basic,pro',
            'custom_domain' => 'nullable|string|regex:/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/|unique:domains,domain|max:50',
            'theme_id' => ['nullable', 'integer', 'exists:themes,id'],
        ]);

        $user = Auth::user();

        $store = $creator->create($user, $request->plan, $request->custom_domain, $request->theme_id);

        return redirect()->route('stores.show', $store->id)->with('success', 'Магазин создан!');
    }
    public function show(Store $store)
    {
        $stores = Store::with(['owner', 'theme', 'domains'])
            ->orderByDesc('created_at')
            ->paginate(10);
        return view('stores.show', compact('stores'));
    }
    public function index(Request $request)
    {


        return view('stores.index', compact('stores'));
    }
}
