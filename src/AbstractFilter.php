<?php

namespace LaraCombs\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Traits\Macroable;
use JsonSerializable;
use LaraCombs\Table\Traits\AuthorizationTrait;
use LaraCombs\Table\Traits\HasComponentTrait;
use LaraCombs\Table\Traits\HasSharedDataTrait;
use LaraCombs\Table\Traits\HasUriKeyTrait;
use LaraCombs\Table\Traits\MakeableTrait;

abstract class AbstractFilter implements JsonSerializable
{
    use AuthorizationTrait;
    use HasComponentTrait;
    use HasUriKeyTrait;
    use HasUriKeyTrait {
        uriKeyFallback as protected uriKeyFallbackFromTrait;
    }
    use HasSharedDataTrait;
    use Macroable;
    use MakeableTrait;

    /**
     * The label for this filter.
     *
     * @var string
     */
    public string $label;

    /**
     * The attribute column name for the filter.
     */
    public string $attribute;

    /**
     * The LaraCombs Table Element Type.
     */
    protected string $type = 'Filter';

    /**
     * Create a new filter element.
     *
     * @param string  $label
     * @param string  $attribute
     */
    public function __construct(string $label, string $attribute)
    {
        $this->label = $label;
        $this->attribute = $attribute;
    }

    /**
     * Return a default URI key as fallback for this filter.
     */
    protected function uriKeyFallback(Request $request): string
    {
        return $this->attribute . '_' . $this->uriKeyFallbackFromTrait($request);
    }

    /**
     * Apply the filter to the given table query.
     *
     * @param \Illuminate\Http\Request               $request
     * @param \Illuminate\Database\Eloquent\Builder  $query
     * @param mixed                                  $case
     * @param mixed                                  $value
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    abstract public function apply(Request $request, Builder $query, mixed $case, mixed $value): Builder;

    /**
     * @param \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return string
     */
    protected function likeOperator(Builder $query): string
    {
        return $query->getConnection()->getDriverName() == 'pgsql' ? 'ILIKE' : 'LIKE';
    }

    /**
     * Determine a custom uriKey for this filter.
     *
     * @param string  $uriKey
     *
     * @return static
     */
    public function setUriKey(string $uriKey): static
    {
        $this->uriKey = $uriKey;

        return $this;
    }

    /**
     * Specify data that should be serialized to JSON for the filter.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $request = app(Request::class);
        $this->resolveUriKey($request);

        return array_merge([
            'key' => $this->uriKey,
            'component' => $this->component($request),
            'label' => $this->label,
        ], $this->sharedData($request));
    }
}
