@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Создать новый магазин</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('shop.store') }}">
                            @csrf

                            <!-- Информация о магазине -->
                            <div class="mb-4">
                                <h5>Информация о магазине</h5>

                                <div class="mb-3">
                                    <label for="shop_name" class="form-label">Название магазина</label>
                                    <input id="shop_name" type="text"
                                           class="form-control @error('shop_name') is-invalid @enderror"
                                           name="shop_name" value="{{ old('shop_name') }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="shop_domain" class="form-label">Адрес магазина</label>
                                    <div class="input-group">
                                        <input id="shop_domain" type="text"
                                               class="form-control @error('shop_domain') is-invalid @enderror"
                                               name="shop_domain" value="{{ old('shop_domain') }}" required>
                                        <span class="input-group-text">.{{ config('tenancy.central_domains')[0] }}</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="plan_id" class="form-label">Тарифный план</label>
                                    <select id="plan_id" name="plan_id" class="form-select" required>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}">{{ $plan->name }} - ${{ $plan->price }}/мес</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Данные владельца -->
                            <div class="mb-4">
                                <h5>Данные владельца</h5>

                                <div class="mb-3">
                                    <label for="name" class="form-label">Ваше имя</label>
                                    <input id="name" type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           name="name" value="{{ old('name') }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input id="email" type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           name="email" value="{{ old('email') }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Пароль</label>
                                    <input id="password" type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           name="password" required>
                                </div>

                                <div class="mb-3">
                                    <label for="password-confirm" class="form-label">Подтвердите пароль</label>
                                    <input id="password-confirm" type="password"
                                           class="form-control"
                                           name="password_confirmation" required>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    Создать магазин
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
