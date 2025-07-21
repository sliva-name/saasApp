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

        $filters = array_filter([
            'price_min' => $request->input('price_min'),
            'price_max' => $request->input('price_max'),
            'category_id' => $request->input('category_id'),
        ]);

        return $this->searchService
            ->model(Product::class)
            ->query($query)
            ->withFilters($filters)
            ->page((int) $request->input('page', 1))
            ->perPage((int) $request->input('per_page', 15))
            ->search();
    }
}
