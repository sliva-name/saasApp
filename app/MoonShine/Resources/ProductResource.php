<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Product;

use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use VI\MoonShineSpatieMediaLibrary\Fields\MediaLibrary;

/**
 * @extends ModelResource<Product>
 */
class ProductResource extends ModelResource
{

    protected string $model = Product::class;

    protected string $title = 'Products';


    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
        ];
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function formFields(): iterable
    {
        return [
            Box::make([
                ID::make(),
                Text::make('Название', 'name')->required(),
                Textarea::make('Описание', 'description'),
                MediaLibrary::make('Галерея', 'image'),
                Number::make('Цена', 'price')->min(0)->step(0.01)->required(),
                Number::make('Остаток на складе', 'stock')->min(0),
                BelongsTo::make('Категория', 'category', resource: CategoryResource::class)->nullable(),
            ])
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Название', 'name')->required(),
            Textarea::make('Описание', 'description'),
            Number::make('Цена', 'price')->min(0)->step(0.01)->required(),
            Number::make('Остаток на складе', 'stock')->min(0),
            BelongsTo::make('Категория', 'category', resource: CategoryResource::class)->nullable(),
        ];
    }

    /**
     * @param Product $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [];
    }
}
