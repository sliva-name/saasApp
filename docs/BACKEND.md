# Backend Documentation

## 🏗️ Обзор бэкенда

Бэкенд приложения построен на Laravel 12 с использованием мультитенантной архитектуры. Основные технологии:

- **Laravel 12** - основной фреймворк
- **Stancl Tenancy** - мультитенантность
- **Laravel Scout + MeiliSearch** - поиск
- **Spatie Media Library** - управление медиа
- **Spatie Permission** - разрешения
- **MoonShine** - админ-панель

## 📁 Структура бэкенда

```
app/
├── Console/           # Artisan команды
│   └── Commands/      # Пользовательские команды
├── Domains/           # Доменная логика
│   ├── Product/       # Домен продуктов
│   ├── Store/         # Домен магазинов
│   └── Theme/         # Домен тем
├── Helpers/           # Вспомогательные классы
├── Http/              # HTTP слой
│   ├── Controllers/   # Контроллеры
│   ├── Requests/      # Валидация запросов
│   └── Resources/     # API ресурсы
├── Listeners/         # Слушатели событий
├── Models/            # Eloquent модели
├── MoonShine/         # Админ-панель
├── Providers/         # Сервис-провайдеры
└── Services/          # Бизнес-логика
```

## 🏪 Модели данных

### Store (Магазин)

Основная модель тенанта:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Store extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'user_id',
        'plan',
        'slug',
        'theme_id',
    ];

    public static function getCustomColumns(): array
    {
        return array_merge(parent::getCustomColumns(), [
            'user_id',
            'plan',
            'slug',
            'theme_id',
        ]);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }
}
```

### Product (Товар)

Модель товара с поиском и медиа:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes, Searchable, HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'category_id',
        'tenant_id',
    ];

    protected $casts = [
        'attributes' => 'array',
    ];

    /**
     * Настройка данных для индексации в MeiliSearch
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'tenant_id' => $this->tenant_id,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (float) $this->price,
            'stock' => (int) $this->stock,
            'category_id' => (int) $this->category_id,
            'image_url' => $this->getFirstMediaUrl('image'),
            'created_at' => $this->created_at->timestamp,
        ];
    }

    protected static function booted()
    {
        static::creating(function ($product) {
            $product->slug = Str::slug($product->name);
            $product->tenant_id ??= tenant('id'); 
        });
        
        static::addGlobalScope('tenant', function ($builder) {
            $builder->where('tenant_id', tenant()->id);
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->useDisk('public');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
```

### Category (Категория)

Древовидная структура категорий:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'sort_order',
        'tenant_id',
    ];

    protected static function booted()
    {
        static::creating(function ($category) {
            $category->tenant_id ??= tenant('id');
        });
        
        static::addGlobalScope('tenant', function ($builder) {
            $builder->where('tenant_id', tenant()->id);
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
```

## 🎮 Контроллеры

### StoreController

Управление созданием магазинов:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Store;
use App\Services\StoreCreator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    public function create()
    {
        $plans = Plan::all();
        return view('stores.create', compact('plans'));
    }

    public function store(Request $request, StoreCreator $creator)
    {
        $request->validate([
            'plan' => 'required|in:Free,Basic,Pro',
            'custom_domain' => 'nullable|string|regex:/^[a-z0-9]+([\-]?[a-z0-9]+)*(\.[a-z]{2,})+$/i|unique:domains,domain|max:50',
            'theme_id' => ['nullable', 'integer', 'exists:themes,id'],
        ]);

        $user = Auth::user();
        $store = $creator->create($user, $request->plan, $request->custom_domain, $request->theme_id);

        return redirect()->route('stores.show', $store->id)
            ->with('success', 'Магазин создан!');
    }

    public function index()
    {
        $stores = Store::with(['owner', 'theme', 'domains'])
            ->orderByDesc('created_at')
            ->paginate(10);
            
        return view('stores.index', compact('stores'));
    }
}
```

### SearchController

Поиск и фильтрация товаров:

```php
<?php

namespace App\Http\Controllers;

use App\Services\ProductSearchService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    public function __construct(
        private ProductSearchService $searchService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $filters = $this->parseFilters($request);
        $page = $request->get('page', 1);

        $results = $this->searchService->search($query, $filters, $page);

        return response()->json($results);
    }

    public function suggest(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['suggestions' => []]);
        }

        $suggestions = $this->searchService->getSuggestions($query);

        return response()->json(['suggestions' => $suggestions]);
    }

    public function getProducts(Request $request): JsonResponse
    {
        $filters = $this->parseFilters($request);
        $page = $request->get('page', 1);

        $results = $this->searchService->getProducts($filters, $page);

        return response()->json($results);
    }

    public function getCategoryProducts(string $slug, Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        
        $results = $this->searchService->getProductsByCategory($slug, $page);

        return response()->json($results);
    }

    private function parseFilters(Request $request): array
    {
        return [
            'categories' => $request->get('categories', []),
            'price_min' => $request->get('price_min'),
            'price_max' => $request->get('price_max'),
            'in_stock' => $request->boolean('in_stock'),
        ];
    }
}
```

### ProductController

Управление товарами:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function show(string $slug): JsonResponse
    {
        $product = Product::where('slug', $slug)
            ->with(['category', 'media'])
            ->firstOrFail();

        return response()->json(new ProductResource($product));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product = Product::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $product->addMedia($image)->toMediaCollection('image');
            }
        }

        return response()->json(new ProductResource($product), 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $product->update($validated);

        return response()->json(new ProductResource($product));
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(['message' => 'Товар удален']);
    }
}
```

## 🔧 Сервисы

### StoreCreator

Создание новых магазинов:

```php
<?php

namespace App\Services;

use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Str;

class StoreCreator
{
    public function create(User $user, string $plan, ?string $customDomain = null, ?int $themeId = null): Store
    {
        $slug = $this->generateSlug($user);

        $store = Store::create([
            'user_id' => $user->id,
            'plan' => $plan,
            'slug' => $slug,
            'theme_id' => $themeId,
        ]);

        $domain = $customDomain ?: $this->generateUniqueSubdomain($user, $slug);

        $store->domains()->create([
            'domain' => $domain,
        ]);

        return $store;
    }

    protected function generateSlug(User $user): string
    {
        return Str::slug($user->name) . '-' . Str::random(6);
    }

    protected function generateUniqueSubdomain(User $user, ?string $slug = null): string
    {
        $slug ??= $this->generateSlug($user);
        return "{$slug}.localhost"; // TODO: Переделать для продакшена
    }
}
```

### ProductSearchService

Специализированный поиск товаров:

```php
<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductSearchService
{
    public function search(string $query, array $filters = [], int $page = 1): array
    {
        $searchService = new SearchService();
        
        return $searchService
            ->model(Product::class)
            ->query($query)
            ->withFilters($filters)
            ->page($page)
            ->perPage(15)
            ->facets(['category_id', 'price'])
            ->search();
    }

    public function getProducts(array $filters = [], int $page = 1): array
    {
        $query = Product::with(['category', 'media']);

        // Применяем фильтры
        if (!empty($filters['categories'])) {
            $query->whereIn('category_id', $filters['categories']);
        }

        if (isset($filters['price_min'])) {
            $query->where('price', '>=', $filters['price_min']);
        }

        if (isset($filters['price_max'])) {
            $query->where('price', '<=', $filters['price_max']);
        }

        if (!empty($filters['in_stock'])) {
            $query->where('stock', '>', 0);
        }

        $products = $query->paginate(15, ['*'], 'page', $page);

        return [
            'data' => ProductResource::collection($products),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ];
    }

    public function getProductsByCategory(string $slug, int $page = 1): array
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $query = Product::with(['category', 'media'])
            ->where('category_id', $category->id);

        $products = $query->paginate(15, ['*'], 'page', $page);

        return [
            'data' => ProductResource::collection($products),
            'category' => $category,
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ];
    }

    public function getSuggestions(string $query): array
    {
        return Product::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->limit(5)
            ->pluck('name')
            ->toArray();
    }
}
```

### SearchService

Универсальный сервис поиска:

```php
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

    public function model(string $modelClass): self
    {
        if (!is_subclass_of($modelClass, Model::class)) {
            throw new \InvalidArgumentException("{$modelClass} is not a valid Eloquent model.");
        }

        $this->modelClass = $modelClass;
        return $this;
    }

    public function query(?string $query): self
    {
        $this->query = $query ?? '';
        return $this;
    }

    public function withFilters(array $filters): self
    {
        $this->filters = $filters;
        return $this;
    }

    public function orderBy(string $field, string $direction = 'asc'): self
    {
        $this->sortBy[] = "{$field}:{$direction}";
        return $this;
    }

    public function facets(array $fields): self
    {
        $this->facets = $fields;
        return $this;
    }

    public function page(int $page): self
    {
        $this->page = max(1, $page);
        return $this;
    }

    public function perPage(int $perPage): self
    {
        $this->perPage = $perPage;
        return $this;
    }

    public function search(): array
    {
        $builder = $this->modelClass::search($this->query, function (Indexes $engine, string $query, array $options) {
            $options['filter'] = $this->buildFilterString();
            $options['limit'] = $this->perPage;
            $options['offset'] = ($this->page - 1) * $this->perPage;

            if (!empty($this->sortBy)) {
                $options['sort'] = $this->sortBy;
            }

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

    protected function buildFilterString(): string
    {
        $filters = [];

        foreach ($this->filters as $key => $value) {
            $filters[] = $this->parseFilter($key, $value);
        }

        return implode(' AND ', array_filter($filters));
    }

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

    protected function formatValue($value): string
    {
        if (is_numeric($value)) {
            return $value;
        }

        return "'" . addslashes($value) . "'";
    }

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
```

## 🔐 Middleware

### Тенантские middleware

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Инициализация тенанта по домену
        $tenancyMiddleware = new InitializeTenancyByDomain();
        $request = $tenancyMiddleware->handle($request, $next);

        // Предотвращение доступа с центральных доменов
        $preventAccessMiddleware = new PreventAccessFromCentralDomains();
        $request = $preventAccessMiddleware->handle($request, $next);

        return $next($request);
    }
}
```

## 📊 API Resources

### ProductResource

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'image_url' => $this->getFirstMediaUrl('image'),
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
```

## 🎯 Artisan Commands

### Команды для тенантов

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Store;

class SetupTenant extends Command
{
    protected $signature = 'setup:tenant {store_id}';
    protected $description = 'Настройка нового тенанта';

    public function handle()
    {
        $storeId = $this->argument('store_id');
        $store = Store::findOrFail($storeId);

        $this->info("Настройка тенанта: {$store->slug}");

        // Переключение на тенанта
        tenancy()->initialize($store);

        // Выполнение миграций
        $this->call('migrate', ['--force' => true]);

        // Заполнение тестовыми данными
        $this->call('db:seed', ['--class' => 'ProductSeeder']);

        // Индексация в поиск
        $this->call('scout:import', ['model' => 'App\\Models\\Product']);

        $this->info('Тенант настроен успешно!');
    }
}
```

### Команды для поиска

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class ReindexTenantProductsForScout extends Command
{
    protected $signature = 'scout:reindex-tenant-products';
    protected $description = 'Переиндексация товаров тенанта в поиск';

    public function handle()
    {
        $this->info('Начинаем переиндексацию товаров...');

        Product::chunk(100, function ($products) {
            foreach ($products as $product) {
                $product->searchable();
            }
        });

        $this->info('Переиндексация завершена!');
    }
}
```

## 🔧 Конфигурация

### Tenancy конфигурация

```php
<?php

// config/tenancy.php

return [
    'tenant_model' => \App\Models\Store::class,
    'id_generator' => Stancl\Tenancy\UUIDGenerator::class,

    'domain_model' => Domain::class,

    'central_domains' => [
        '127.0.0.1',
        'localhost',
    ],

    'bootstrappers' => [
        Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\CacheTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\QueueTenancyBootstrapper::class,
    ],

    'database' => [
        'central_connection' => env('DB_CONNECTION', 'central'),
        'template_tenant_connection' => null,
        'prefix' => 'tenant',
        'suffix' => '',
    ],

    'cache' => [
        'tag_base' => 'tenant',
    ],

    'filesystem' => [
        'suffix_base' => 'tenant',
        'disks' => [
            'local',
            'public',
        ],
    ],
];
```

### Scout конфигурация

```php
<?php

// config/scout.php

return [
    'driver' => env('SCOUT_DRIVER', 'meilisearch'),

    'meilisearch' => [
        'host' => env('MEILISEARCH_HOST', 'http://localhost:7700'),
        'key' => env('MEILISEARCH_KEY', null),
        'index-settings' => [
            \App\Models\Product::class => [
                'filterableAttributes' => ['tenant_id', 'category_id', 'price'],
                'sortableAttributes' => ['price', 'created_at'],
                'searchableAttributes' => ['name', 'description'],
            ],
        ],
    ],
];
```

## 🧪 Тестирование

### Feature тесты

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Store;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_search_products()
    {
        // Создание тенанта
        $store = Store::factory()->create();
        
        // Переключение на тенанта
        tenancy()->initialize($store);

        // Создание товаров
        Product::factory()->count(5)->create();

        $response = $this->get('/api/products?q=test');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'pagination'
            ]);
    }

    public function test_can_filter_products_by_category()
    {
        $store = Store::factory()->create();
        tenancy()->initialize($store);

        $response = $this->get('/api/products?categories[]=1');

        $response->assertStatus(200);
    }
}
```

### Unit тесты

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ProductSearchService;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductSearchServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_search_products()
    {
        $service = new ProductSearchService();
        
        $results = $service->search('test', [], 1);
        
        $this->assertIsArray($results);
        $this->assertArrayHasKey('hits', $results);
        $this->assertArrayHasKey('pagination', $results);
    }
}
```

## 🔄 События и слушатели

### События

```php
<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Product $product
    ) {}
}
```

### Слушатели

```php
<?php

namespace App\Listeners;

use App\Events\ProductCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReindexProductOnMediaAdded implements ShouldQueue
{
    public function handle(ProductCreated $event): void
    {
        // Переиндексация товара в поиск
        $event->product->searchable();
    }
}
```

## 📚 Дополнительные ресурсы

- [Laravel Documentation](https://laravel.com/docs)
- [Tenancy for Laravel](https://tenancyforlaravel.com/)
- [Laravel Scout](https://laravel.com/docs/scout)
- [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary)
- [Spatie Permission](https://spatie.be/docs/laravel-permission)
- [MoonShine Documentation](https://moonshine.cutcode.dev/)
