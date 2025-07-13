<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stancl\Tenancy\Database\Models\Tenant;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    public function create()
    {
        $plans = Plan::where('is_active', true)->get();
        return view('tenants.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|unique:domains',
            'plan_id' => 'required|exists:plans,id',
        ]);

        $tenant = Tenant::create([
            'id' => Str::random(10),
            'tenancy_db_name' => 'tenant_' . Str::random(10),
        ]);

        $tenant->domains()->create([
            'domain' => $request->domain . '.' . config('tenancy.central_domains')[0],
        ]);

        // Создаем подписку
        Subscription::create([
            'user_id' => auth()->id(),
            'plan_id' => $request->plan_id,
            'tenant_id' => $tenant->id,
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
            'is_active' => true,
        ]);

        return redirect()->route('admin.tenants.index')->with('success', 'Магазин успешно создан!');
    }
}
