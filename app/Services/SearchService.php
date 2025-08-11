<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Builder;
use MeiliSearch\Endpoints\Indexes;

class SearchService
{
    protected string $query = '';
    protected array $filters = [];
    protected int $page = 1;
    protected int $perPage = 15;
    protected string $modelClass;
    protected array $sortBy = [];
    protected array $facets = [];

    /**
     * Устанавливает модель для поиска
     */
    public function model(string $modelClass): self
    {
        if (!is_subclass_of($modelClass, Model::class)) {
            throw new \InvalidArgumentException("{$modelClass} is not a valid Eloquent model.");
        }

        $this->modelClass = $modelClass;
        return $this;
    }

    /**
     * Устанавливает строку запроса
     */
    public function query(?string $query): self
    {
        $this->query = $query ?? '';
        return $this;
    }

    /**
     * Устанавливает массив фильтров
     */
    public function withFilters(array $filters): self
    {
        $this->filters = $filters;
        return $this;
    }

    /**
     * Устанавливает сортировку
     */
    public function orderBy(string $field, string $direction = 'asc'): self
    {
        $this->sortBy[] = "{$field}:{$direction}";
        return $this;
    }

    /**
     * Устанавливает поля для фасеточной выборки (распределения)
     */
    public function facets(array $fields): self
    {
        $this->facets = $fields;
        return $this;
    }

    /**
     * Устанавливает текущую страницу
     */
    public function page(int $page): self
    {
        $this->page = max(1, $page);
        return $this;
    }

    /**
     * Устанавливает количество элементов на странице
     */
    public function perPage(int $perPage): self
    {
        $this->perPage = $perPage;
        return $this;
    }

    /**
     * Выполняет поиск
     */
    public function search(): array
    {
        $builder = $this->modelClass::search($this->query, function (Indexes $engine, string $query, array $options) {
            $options['filter'] = $this->buildFilterString();
            $options['limit'] = $this->perPage;
            $options['offset'] = ($this->page - 1) * $this->perPage;

            // Добавляем сортировку
            if (!empty($this->sortBy)) {
                $options['sort'] = $this->sortBy;
            }

            // Запрашиваем фасеточные распределения
            if (!empty($this->facets)) {
                $options['facets'] = $this->facets;
            }

            return $engine->search($query, $options);
        });

        $result = $builder->raw();

        return [
            'hits' => $result['hits'] ?? [],
            'pagination' => $this->buildPagination($result['nbHits'] ?? 0),
            'facets' => $result['facetDistribution'] ?? [],
        ];
    }

    /**
     * Формирует строку фильтров MeiliSearch
     */
    protected function buildFilterString(): string
    {
        $filters = [];

        foreach ($this->filters as $key => $value) {
            $filters[] = $this->parseFilter($key, $value);
        }

        return implode(' AND ', array_filter($filters));
    }

    /**
     * Парсим строку фильтров MeiliSearch
     */
    protected function parseFilter(string $key, mixed $value): ?string
    {
        $patterns = [
            '/^(.*)_min$/'      => fn($f, $v) => "{$f} >= " . $this->formatValue($v),
            '/^(.*)_max$/'      => fn($f, $v) => "{$f} <= " . $this->formatValue($v),
            '/^(.*)_not_null$/' => fn($f)     => "{$f} IS NOT NULL",
            '/^(.*)_null$/'     => fn($f)     => "{$f} IS NULL",
            '/^(.*)_in$/'       => fn($f, $v) => is_array($v) ? '(' . implode(' OR ', array_map(fn($val) => "{$f} = " . $this->formatValue($val), $v)) . ')' : null,
            '/^(.*)_not_in$/'   => fn($f, $v) => is_array($v) ? '(' . implode(' AND ', array_map(fn($val) => "{$f} != " . $this->formatValue($val), $v)) . ')' : null,
        ];

        foreach ($patterns as $pattern => $callback) {
            if (preg_match($pattern, $key, $matches)) {
                return $callback($matches[1], $value);
            }
        }

        return "{$key} = " . $this->formatValue($value);
    }


    /**
     * Экранирует значения фильтра
     */
    protected function formatValue($value): string
    {
        if (is_numeric($value)) {
            return $value;
        }

        return "'" . addslashes($value) . "'";
    }

    /**
     * Строит структуру пагинации
     */
    protected function buildPagination(int $total): array
    {
        return [
            'total' => $total,
            'per_page' => $this->perPage,
            'current_page' => $this->page,
            'last_page' => (int) ceil($total / $this->perPage),
            'from' => ($this->page - 1) * $this->perPage + 1,
            'to' => min($this->page * $this->perPage, $total),
        ];
    }
}
