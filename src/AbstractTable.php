<?php

namespace LaraCombs\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use JsonSerializable;
use LaraCombs\Table\Support\TranslationData;

/**
 * @template TKey of array-key
 */
abstract class AbstractTable implements JsonSerializable
{
    use Macroable;

    /**
     * The URI key for the given table.
     */
    protected ?string $uriKey = null;

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
     * The array of table headings.
     *
     * @var \Illuminate\Support\Collection<TKey, mixed>
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

        if ($column && in_array($column, $this->headings->pluck('attribute')->toArray())) {
            return $column;
        }

        return $this->headings->first()['attribute'];
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
        return [20, 50, 100];
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
     * Get all available standalone actions for this table.
     *
     * @return array<\LaraCombs\Table\AbstractAction>
     */
    public function standaloneActions(Request $request): array
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
     * Set the URI key for the given table.
     */
    protected function setUriKey(Request $request): void
    {
        $this->uriKey = empty($this->uriKey) ? $this->uriKeyFallback($request) : trim(Str::lower($this->uriKey));
    }

    /**
     * Return a default URI key as fallback for this table.
     */
    protected function uriKeyFallback(Request $request): string
    {
        return Str::snake(class_basename(get_called_class()));
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

        $instance = $this->orderQuery($request, $instance);

        $perPage = $request->integer($this->uriKey . '_per_page');

        $instance = $instance->paginate(
            $perPage && $perPage > 0 ? $perPage : $this->perPageOptions($request)[0],
            ['*'],
            $this->uriKey . '_page'
        );

        return $this->mapResourcesCollection($instance);
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
        $data = [];
        $this->columns->each(function (AbstractColumn $column) use (&$data, $resource) {
            $data[] = $column->forResource($resource)->jsonSerialize();
        });

        return $data;
    }

    /**
     * Determine the array of authorized table columns.
     */
    protected function setColumns(Request $request): void
    {
        $this->columns = collect(Arr::where(
            $this->columns($request),
            fn (AbstractColumn $column) => $column->authorize($request)
        ));
    }

    /**
     * Set the array of table headings.
     */
    protected function setHeadings(): void
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
     * @param  \Illuminate\Http\Request  $request
     * @return array<\LaraCombs\Table\AbstractAction>
     */
    protected function resolveActions(Request $request): array
    {
        // @Todo: implement
        return [];
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<\LaraCombs\Table\AbstractAction>
     */
    protected function resolveStandaloneActions(Request $request): array
    {
        // @Todo: implement
        return [];
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<\LaraCombs\Table\AbstractFilter>
     */
    protected function resolveFilters(Request $request): array
    {
        // @Todo: implement
        return [];
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
     * Specify data that should be serialized to JSON for the table.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $request = app(Request::class);
        $this->setUriKey($request);
        $this->setColumns($request);
        $this->setHeadings();

        $actions = $this->resolveActions($request);
        $standaloneActions = $this->resolveStandaloneActions($request);

        return [
            'paginator' => $this->paginator($request),
            'key' => $this->uriKey,
            'headings' => $this->headings,
            'isSearchable' => ! empty($this->search($request)),
            'actions' => $actions,
            'standaloneActions' => $standaloneActions,
            'filters' => $this->resolveFilters($request),
            'hasActions' => count($actions) > 0 || count($standaloneActions) > 0,
            'debounce' => $this->debounce($request),
            'bindings' => $this->bindings,
            'orderColumn' => $this->orderColumn,
            'orderDirection' => $this->orderDirection,
            'translations' => (new TranslationData())(),
            'search' => $request->input($this->uriKey . '_search'),
            'queryParams' => $request->query(),
            'searchValue' => (string) $request->input($this->uriKey . '_search'),
        ];
    }
}
