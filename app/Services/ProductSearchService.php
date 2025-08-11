<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;
use MeiliSearch\Endpoints\Indexes;

class ProductSearchService
{
    public function __construct(
        protected SearchService $searchService
    ) {}

    public function search(Request $request): array
    {
        $query = $request->input('query', '');

        $filters = [];

        // Обработка фильтров по цене из priceRange
        if ($request->has('priceRange')) {
            $priceRange = $request->input('priceRange');
            if (isset($priceRange['min']) && $priceRange['min'] !== null && $priceRange['min'] !== '') {
                $filters['price_min'] = $priceRange['min'];
            }
            if (isset($priceRange['max']) && $priceRange['max'] !== null && $priceRange['max'] !== '') {
                $filters['price_max'] = $priceRange['max'];
            }
        }

        // Обработка фильтров по категориям
        if ($request->has('categories') && is_array($request->input('categories'))) {
            $categories = $request->input('categories');
            if (!empty($categories)) {
                $filters['category_id_in'] = $categories;
            }
        }

        // Обработка фильтров по наличию
        if ($request->has('availability') && is_array($request->input('availability'))) {
            $availability = $request->input('availability');
            if (!empty($availability)) {
                $stockConditions = [];
                foreach ($availability as $type) {
                    switch ($type) {
                        case 'in_stock':
                            $stockConditions[] = 'stock > 0';
                            break;
                        case 'out_of_stock':
                            $stockConditions[] = 'stock = 0';
                            break;
                        case 'pre_order':
                            $stockConditions[] = 'stock = -1'; // Предполагаем, что -1 означает "под заказ"
                            break;
                    }
                }
                if (!empty($stockConditions)) {
                    // Для простоты пока используем только один тип наличия
                    if (in_array('in_stock', $availability)) {
                        $filters['stock_min'] = 1;
                    } elseif (in_array('out_of_stock', $availability)) {
                        $filters['stock_max'] = 0;
                    }
                }
            }
        }

        $searchService = $this->searchService
            ->model(Product::class)
            ->query($query)
            ->withFilters($filters)
            ->facets(['category_id'])
            ->page((int) $request->input('page', 1))
            ->perPage((int) $request->input('per_page', 15));

        // Добавляем сортировку
        $sort = $request->input('sort', 'relevance');
        if ($sort !== 'relevance') {
            $searchService = $this->applySorting($searchService, $sort);
        }

        return $searchService->search();
    }

    protected function applySorting($searchService, string $sort)
    {
        switch ($sort) {
            case 'price_asc':
                return $searchService->orderBy('price', 'asc');
            case 'price_desc':
                return $searchService->orderBy('price', 'desc');
            case 'name_asc':
                return $searchService->orderBy('name', 'asc');
            case 'name_desc':
                return $searchService->orderBy('name', 'desc');
            case 'newest':
                return $searchService->orderBy('created_at', 'desc');
            default:
                return $searchService;
        }
    }
}
