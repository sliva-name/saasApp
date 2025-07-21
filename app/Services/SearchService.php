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

            return $engine->search($query, $options);
        });

        $result = $builder->raw();

        return [
            'hits' => $result['hits'],
            'pagination' => $this->buildPagination($result['nbHits']),
        ];
    }

    /**
     * Формирует строку фильтров MeiliSearch
     */
    protected function buildFilterString(): string
    {
        $filters = [];

        foreach ($this->filters as $key => $value) {
            if (str_ends_with($key, '_min')) {
                $field = str_replace('_min', '', $key);
                $filters[] = "{$field} >= {$value}";
            } elseif (str_ends_with($key, '_max')) {
                $field = str_replace('_max', '', $key);
                $filters[] = "{$field} <= {$value}";
            } elseif (str_ends_with($key, '_not_null')) {
                $field = str_replace('_not_null', '', $key);
                $filters[] = "{$field} IS NOT NULL";
            } elseif (str_ends_with($key, '_null')) {
                $field = str_replace('_null', '', $key);
                $filters[] = "{$field} IS NULL";
            } elseif (str_ends_with($key, '_in') && is_array($value)) {
                $field = str_replace('_in', '', $key);
                $inValues = implode(' OR ', array_map(fn($v) => "{$field} = " . $this->formatValue($v), $value));
                $filters[] = '(' . $inValues . ')';
            } elseif (str_ends_with($key, '_not_in') && is_array($value)) {
                $field = str_replace('_not_in', '', $key);
                $notInValues = implode(' AND ', array_map(fn($v) => "{$field} != " . $this->formatValue($v), $value));
                $filters[] = '(' . $notInValues . ')';
            } else {
                $filters[] = "{$key} = " . $this->formatValue($value);
            }
        }

        return implode(' AND ', $filters);
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
