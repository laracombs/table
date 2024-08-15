<?php

namespace LaraCombs\Table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Traits\Macroable;
use JsonSerializable;
use LaraCombs\Table\Traits\AuthorizationTrait;
use LaraCombs\Table\Traits\HasClassAndStyleBindingTrait;
use LaraCombs\Table\Traits\HasComponentTrait;
use LaraCombs\Table\Traits\MakeableTrait;

/** @phpstan-consistent-constructor */
abstract class AbstractColumn implements JsonSerializable
{
    use AuthorizationTrait;
    use HasClassAndStyleBindingTrait;
    use HasComponentTrait;
    use Macroable;
    use MakeableTrait;

    /**
     * The LaraCombs Table Element Type.
     */
    protected string $type = 'Column';

    /**
     * The resource associated with the column.
     */
    public Model $resource;

    /**
     * The name displayed in heading of a table.
     */
    public string $name;

    /**
     * The field default value.
     */
    public mixed $default;

    /**
     * The attribute column name for the column.
     */
    public string $attribute;

    /**
     * The callback to be used to resolve the field's value.
     *
     * @var (callable(mixed, mixed, ?string):(mixed))|null
     */
    public mixed $resolveAttributeCallback;

    /**
     * Create a new column element.
     *
     * @param  string  $name
     * @param  string  $attribute
     * @param  (callable(mixed, mixed, ?string):(mixed))|null  $resolveAttributeCallback
     */
    public function __construct(string $name, string $attribute, ?callable $resolveAttributeCallback = null)
    {
        $this->name = $name;
        $this->attribute = $attribute;
        $this->resolveAttributeCallback = $resolveAttributeCallback;
    }

    public function forResource(Model $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Set the default value.
     *
     * @return $this
     */
    public function default(mixed $value): static
    {
        $this->default = $value;

        return $this;
    }

    /**
     * Resolve the default value.
     */
    protected function resolveDefaultValue(): mixed
    {
        return $this->default ?? config('laracombs-table.default_value');
    }

    /**
     * Resolve the column value for display.
     */
    protected function resolveValue(Request $request): mixed
    {
        $value = is_callable($this->resolveAttributeCallback) ?
            call_user_func($this->resolveAttributeCallback, $this->resource, $request) :
            value(data_get($this->resource, $this->attribute));

        return ! is_bool($value) && ! is_int($value) && empty($value) ? $this->resolveDefaultValue() : $value;
    }

    /**
     * Specify data that should be serialized to JSON for the colum.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $request = app(Request::class);

        return [
            'component' => $this->component($request),
            'value' => $this->resolveValue($request),
            'sortable' => $this->sortable ?? false,
            'asHtml' => $this->renderHtml ?? false,
            'attribute' => $this->attribute,
            'bindings' => $this->bindings ?? null,
        ];
    }
}
