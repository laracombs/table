<?php

namespace LaraCombs\Table\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use LaraCombs\Table\AbstractFilter;

/**
 * @method static static make(string $label, string $attribute)
 */
class BooleanFilter extends AbstractFilter
{
    /**
     * @var mixed
     */
    public mixed $trueValue = true;

    /**
     * @var mixed
     */
    public mixed $falseValue = false;

    public string $trueLabel;

    public string $falseLabel;

    /**
     * Create a new filter element.
     *
     * @param string  $label
     * @param string  $attribute
     */
    public function __construct(string $label, string $attribute)
    {
        $this->trueLabel = __('Yes');
        $this->falseLabel = __('No');

        parent::__construct($label, $attribute);
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
    public function apply(Request $request, Builder $query, mixed $case, mixed $value): Builder
    {
        return $query->where($this->attribute, $value == 'y');
    }

    /**
     * Specify additional data that should be serialized to JSON for the colum.
     */
    protected function sharedData(Request $request): array
    {
        return [
            'trueLabel' => $this->trueLabel,
            'falseLabel' => $this->falseLabel,
        ];
    }
}
