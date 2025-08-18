<?php

namespace App\Http\Controllers;

use App\Http\Requests\Store\StoreStoreRequest;
use App\Models\Plan;
use App\Models\Store;
use App\Services\StoreCreator;
use Illuminate\Support\Facades\Auth;
class StoreController extends Controller
{
    public function create()
    {
        $plans = Plan::all();

        return view('stores.create', compact('plans'));
    }

    public function store(StoreStoreRequest $request, StoreCreator $creator)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Необходимо войти в систему для создания магазина.');
        }

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

    public function show(Store $store)
    {
        // Проверяем, что пользователь является владельцем магазина
        $user = Auth::user();
        if (!$user || $store->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('stores.show', compact('store'));
    }

    public function settings()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Необходимо войти в систему для доступа к настройкам.');
        }

        $stores = $user->stores()->with(['theme', 'domains'])->get();
        
        return view('stores.settings', compact('stores'));
    }
}
