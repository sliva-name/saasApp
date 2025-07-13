<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Store;

use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;

/**
 * @extends ModelResource<Store>
 */
class StoreResource extends ModelResource
{
    protected string $model = Store::class;

    protected string $title = 'Магазины';

    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Slug')->required(),

            Select::make('План', 'plan')->options([
                'free' => 'Бесплатный',
                'basic' => 'Базовый',
                'pro' => 'Профессиональный',
            ]),
        ];
    }
    protected function formFields(): iterable
    {
        return [
            Box::make([
                ID::make(),
                Text::make('Slug')->required(),

                Select::make('План', 'plan')->options([
                    'free' => 'Бесплатный',
                    'basic' => 'Базовый',
                    'pro' => 'Профессиональный',
                ]),
            ])
        ];
    }

    protected function detailFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Slug')->required(),

            Select::make('План', 'plan')->options([
                'free' => 'Бесплатный',
                'basic' => 'Базовый',
                'pro' => 'Профессиональный',
            ]),
        ];
    }

    protected function rules(mixed $item): array
    {
        return [
            'slug' => ['required', 'string'],
            'domain' => ['required', 'string'],
            'plan' => ['required', 'in:free,basic,pro'],
        ];
    }
    protected function modifyQueryBuilder(Builder $builder): Builder
    {
        return $builder->where('user_id', auth()->id());
    }
}
