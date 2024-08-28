<?php

namespace LaraCombs\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use JsonSerializable;
use LaraCombs\Table\Events\TableCreated;
use LaraCombs\Table\Events\TableSerialize;
use LaraCombs\Table\Support\ActionCollection;
use LaraCombs\Table\Support\TranslationData;
use LaraCombs\Table\Traits\HasUriKeyTrait;
use LaraCombs\Table\Traits\MakeableTrait;

/**
 * @template TKey of array-key
 * @method static static make(?string $uriKey = null)
 */
abstract class AbstractTable implements JsonSerializable
{
    use HasUriKeyTrait;
    use Macroable;
    use MakeableTrait;

    /**
     * The debounce amount to use when searching in this table.
     */
    public ?int $debounce = null;

    /**
     * The Collection of authorized table columns.
     *
     * @var \Illuminate\Support\Collection<int, \LaraCombs\Table\AbstractColumn>
     */
    protected Collection $columns;

    /**
     * The Collection of authorized table filters.
     *
     * @var \Illuminate\Support\Collection<string, array>
     */
    protected Collection $filters;

    /**
     * The array of active filters.
     *
     * @var string[]
     */
    protected array $activeFilters = [];

    /**
     * The array of active filters cases.
     * @var array<int, array>
     */
    protected array $activeFilterCases = [];

    /**
     * The array of active filters values.
     * @var array<int, array>
     */
    protected array $activeFilterValues = [];

    /**
     * The Collection of authorized table actions.
     *
     * @var \Illuminate\Support\Collection<int, \LaraCombs\Table\AbstractAction>
     */
    protected Collection $actions;

    /**
     * The Collection of authorized table standalone actions.
     *
     * @var \Illuminate\Support\Collection<int, \LaraCombs\Table\AbstractAction>
     */
    protected Collection $standaloneActions;

    /**
     * The array of table headings.
     *
     * @var \Illuminate\Support\Collection<TKey, string>
     */
    protected Collection $headings;

    /**
     * The Class and Style Bindings.
     *
     * @var array<string, array<int, string>>
     */
    protected array $bindings = [
        'classes' => ['table'],
    ];

    /**
     * The order column using to order by default.
     *
     * @var string|null
     */
    protected ?string $orderColumn = null;

    /**
     * The order direction using to order by default.
     *
     * @var string
     */
    protected string $orderDirection = 'desc';

    /**
     * Create a new Table instance.
     *
     * @param string|null  $uriKey
     */
    public function __construct(?string $uriKey = null)
    {
        $this->uriKey = $uriKey;
        $this->dispatchTableCreated();
    }

    /**
     * The resource model for the given table.
     *
     * @return class-string<\Illuminate\Database\Eloquent\Model>
     */
    abstract public function model(): string;

    /**
     * Get the table columns for the given resource.
     *
     * @return array<int, \LaraCombs\Table\AbstractColumn>
     */
    abstract public function columns(Request $request): array;

    /**
     * Get the columns that should be searched.
     *
     * @return array<int, string>
     */
    public function search(Request $request): array
    {
        return [];
    }

    /**
     * Build the query for the given resource.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @return \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>
     */
    public function query(Request $request, Builder $query): Builder
    {
        return $query;
    }

    /**
     * Build the order query for the given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @return \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>
     */
    protected function orderQuery(Request $request, Builder $query): Builder
    {
        $this->orderColumn = $this->orderColumn($request);
        $this->orderDirection = $this->orderDirection($request);

        return $query->orderBy($this->orderColumn, $this->orderDirection);
    }

    /**
     * Get the order column for the current Request.
     *
     * @param \Illuminate\Http\Request  $request
     *
     * @return string
     */
    protected function orderColumn(Request $request): string
    {
        $column = $request->input($this->uriKey . '_order', $this->orderColumn);

        if (
            $column && in_array($column, $this->headings->pluck('attribute')->toArray()) &&
            data_get($this->headings->firstWhere('attribute', $column), 'sortable')
        ) {
            return $column;
        }

        /* @var \Illuminate\Database\Eloquent\Model $instance */
        $instance = app($this->model());

        return $instance->getKeyName();
    }

    /**
     * Get the order direction for the current Request.
     *
     * @param \Illuminate\Http\Request  $request
     *
     * @return string
     */
    protected function orderDirection(Request $request): string
    {
        $direction = $request->input($this->uriKey . '_direction', $this->orderDirection);

        return in_array($direction, ['asc', 'desc']) ? $direction : 'desc';
    }

    /**
     * The configured per page options for this table.
     *
     * @return array<int, int>
     */
    public function perPageOptions(Request $request): array
    {
        return config('laracombs-table.per_page', [20, 50, 100]);
    }

    /**
     * Get all available actions for this table.
     *
     * @return array<\LaraCombs\Table\AbstractAction>
     */
    public function actions(Request $request): array
    {
        return [];
    }

    /**
     * Get all available filters for this table.
     *
     * @return array<\LaraCombs\Table\AbstractAction>
     */
    public function filters(Request $request): array
    {
        return [];
    }

    /**
     * Get the paginator for the given table resources.
     */
    protected function paginator(Request $request): AbstractPaginator
    {
        $instance = $this->getResourcesInstance()
            ->where(fn (Builder $query) => $this->query($request, $query));

        if ($search = $request->input($this->uriKey . '_search')) {
            $instance->where(function (Builder $query) use ($search, $request) {
                foreach ($this->search($request) as $key => $column) {
                    // Todo: Relationships
                    $method = $key === 0 ? 'where' : 'orWhere';
                    $query->{$method}($column, 'like', '%' . $search . '%');
                }
            });
        }

        if ($filters = $request->input($this->uriKey . '_filters')) {
            $this->applyFilters($request, $instance, $filters);
        }

        $instance = $this->orderQuery($request, $instance);

        $instance = $instance->paginate(
            $this->currentPerPage($request),
            ['*'],
            $this->uriKey . '_page'
        )->withQueryString();

        return $this->mapResourcesCollection($instance);
    }

    /**
     * Get the default per page option.
     */
    protected function defaultPerPage(Request $request): int
    {
        return $this->perPageOptions($request)[0];
    }

    /**
     * Get the current per page option.
     */
    protected function currentPerPage(Request $request): int
    {
        $perPage = $request->integer($this->uriKey . '_per_page');

        return $perPage && $perPage > 0 ? $perPage : $this->perPageOptions($request)[0];
    }

    protected function applyFilters(Request $request, Builder $query, string $filters)
    {
        $filters = json_decode(base64_decode($filters), true);

        foreach ($filters as $key => $value) {
            if (! $this->filters->has($key)) {
                continue;
            }
            $this->activeFilters[] = $key;
            $this->activeFilterCases[$key] = data_get($value, 'case');
            $this->activeFilterValues[$key] = $value['value'];
        }

        if (empty($filters)) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($filters, $request) {
            $this->filters
                ->reject(fn (array $filter, string $key) => ! in_array($key, array_keys($filters)))
                ->each(function ($filter) use (&$query, $request, $filters) {
                    /* @var \LaraCombs\Table\AbstractFilter $filter */
                    $filter = $filter['resource'];
                    return $filter->apply(
                        request: $request,
                        query: $query,
                        case: data_get($filters, $filter->uriKey . '.case'),
                        value: $filters[$filter->uriKey]['value']
                    );
                });
        });
    }

    /**
     * Get a new query builder for the model's table.
     *
     * @return \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>
     */
    protected function getResourcesInstance(): Builder
    {
        return app($this->model())->newQuery();
    }

    /**
     * Run a map over each of the Model resources.
     *
     * @param  \Illuminate\Pagination\AbstractPaginator<\Illuminate\Support\Collection>  $resources
     * @return \Illuminate\Pagination\AbstractPaginator
     */
    protected function mapResourcesCollection(AbstractPaginator $resources): AbstractPaginator
    {
        $collection = $resources->getCollection()
            ->map(fn (Model $resource) => $this->mapModelResource($resource));

        return $resources->setCollection($collection);
    }

    /**
     * Map Model resource with table columns.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $resource
     * @return array<int, array<string, mixed>>.
     */
    protected function mapModelResource(Model $resource): array
    {
        $data = [$this->indexColumn($resource)];
        $this->columns->each(function (AbstractColumn $column) use (&$data, $resource) {
            $data[] = $column->forResource($resource)->jsonSerialize();
        });

        return $data;
    }

    /**
     * Get the index column for a Model resource.
     *
     * @param \Illuminate\Database\Eloquent\Model  $resource
     *
     * @return array
     */
    protected function indexColumn(Model $resource): array
    {
        return [
            'component' => null,
            'key' => $resource->getKey(),
        ];
    }

    /**
     * Resolve the Collection of authorized table columns.
     */
    protected function resolveColumns(Request $request): void
    {
        $this->columns = collect($this->columns($request))
            ->reject(fn (AbstractColumn $column) => ! $column->authorize($request));
    }

    /**
     * Resolve the Collection of table headings.
     */
    protected function resolveHeadings(): void
    {
        $this->headings = $this->columns
            ->map(fn (AbstractColumn $column) => [
                'attribute' => $column->attribute,
                'name' => $column->name,
                'classes' => data_get($column->bindings, 'headingClasses', []),
                'styles' => data_get($column->bindings, 'headingStyles', []),
                'sortable' => $column->sortable,
            ]);
    }

    /**
     * Resolve the Collection of table actions.
     */
    protected function resolveActions(Request $request): void
    {
        // @Todo: implement
        $this->actions = new ActionCollection();
        $this->standaloneActions = new ActionCollection();

        foreach ($this->actions($request) as $action) {
            if (! $action->authorize($request)) {
                continue;
            }

            $action->isStandalone($request) ? $this->standaloneActions->push($action) : $this->actions->push($action);
        }
    }

    /**
     * Resolve the Collection of authorized table filters.
     */
    protected function resolveFilters(Request $request): void
    {
        $this->filters = collect($this->filters($request))
            ->reject(fn (AbstractFilter $filter) => ! $filter->authorize($request))
            ->mapWithKeys(function (AbstractFilter $filter) {
                $data = $filter->jsonSerialize();
                $data['resource'] = $filter;

                return [$data['key'] => $data];
            });
    }

    /**
     * Determine the debounce amount in seconds for this table.
     */
    public function debounce(Request $request): int|float
    {
        if ($this->debounce > 0) {
            return $this->debounce;
        }

        $debounce = config('laracombs-table.search_debounce', 0.5);

        return is_float($debounce) || is_int($debounce) ? $debounce : 0.5;
    }

    /**
     * Dispatch the TableCreated event
     *
     * @return void
     */
    protected function dispatchTableCreated(): void
    {
        TableCreated::dispatch($this);
    }

    /**
     * Dispatch the TableSerialize event
     *
     * @return void
     */
    protected function dispatchTableSerialize(): void
    {
        TableSerialize::dispatch($this);
    }

    /**
     * Specify data that should be serialized to JSON for the table.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $request = app(Request::class);
        $this->resolveUriKey($request);
        $this->resolveColumns($request);
        $this->resolveHeadings();
        $this->resolveFilters($request);
        $this->resolveActions($request);
        $paginator = $this->paginator($request);

        $data = [
            'classes' => config('laracombs-table.classes'),
            'key' => $this->uriKey,
            'paginator' => $paginator,
            'headings' => $this->headings,
            'isSearchable' => ! empty($this->search($request)),
            'actions' => $this->actions,
            'standaloneActions' => $this->standaloneActions,
            'filters' => $this->filters->map(fn (array $filter) => Arr::except($filter, 'resource')),
            'activeFilters' => $this->activeFilters,
            'activeFilterCases' => $this->activeFilterCases,
            'activeFilterValues' => $this->activeFilterValues,
            'actionable' => $this->actions->isNotEmpty() || $this->standaloneActions->isNotEmpty(),
            'debounce' => $this->debounce($request),
            'bindings' => $this->bindings,
            'orderColumn' => $this->orderColumn,
            'orderDirection' => $this->orderDirection,
            'translations' => (new TranslationData())(),
            'search' => $request->input($this->uriKey . '_search'),
            'queryParams' => $request->query(),
            'searchValue' => (string) $request->input($this->uriKey . '_search'),
            'perPage' => $this->currentPerPage($request),
            'perPageOptions' => $this->perPageOptions($request),
        ];

        $this->dispatchTableSerialize();

        return $data;
    }
}
