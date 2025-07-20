<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Helpers\TenantContext;
use App\MoonShine\Pages\CategoryTreePage;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Leeto\MoonShineTree\Resources\TreeResource;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\DependencyInjection\RouterContract;
use MoonShine\Contracts\Core\PagesContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Collections\Fields;
use MoonShine\UI\Components\Badge;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Link;
use MoonShine\UI\Fields\ID;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Fields\Text;

/**
 * @extends ModelResource<Category>
 */
class CategoryResource extends TreeResource
{
    protected string $model = Category::class;

    public string $title = 'Категории';

    protected string $column = 'name';

    protected string $sortColumn = 'sorting';


    protected function pages(): array
    {
        return [
            CategoryTreePage::class,
            FormPage::class,
            DetailPage::class,
        ];
    }

    protected function formFields(): iterable
    {


//                TenantContext::forEachTenant(function () {
//            \App\Models\Category::all()->each->delete();
//        });
        return [
            ID::make()->sortable(),

            Text::make('Название', 'name')->required()->reactive(function(Fields $fields, ?string $value): Fields {
                return tap($fields, static fn ($fields) => $fields
                    ->findByColumn('slug')
                    ?->setValue(str($value ?? '')->slug()->value())
                );
            }),
            Slug::make('Slug', 'slug')->from('name')->unique()->live(),
            BelongsTo::make('Родительская категория', 'parent', resource: self::class)
                ->nullable()
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return [
            ID::make()->sortable(),

            Text::make('Название', 'name')->required()->reactive(function(Fields $fields, ?string $value): Fields {
                return tap($fields, static fn ($fields) => $fields
                    ->findByColumn('slug')
                    ?->setValue(str($value ?? '')->slug()->value())
                );
            }),
            Slug::make('Slug', 'slug')->from('name')->unique()->live(),
            BelongsTo::make('Родительская категория', 'parent', resource: self::class)
                ->nullable()
        ];
    }

    /**
     * @param Category $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'name' => ['required', 'string'],
            'slug' => ['required', 'string', 'unique:categories,slug,' . $item?->id],
        ];
    }

    public function treeKey(): ?string
    {
        return 'parent_id';
    }

    public function sortKey(): string
    {
        return 'sorting';
    }

    public function showBadge(): bool
    {
        return true;
    }
}
