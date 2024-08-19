<?php

namespace LaraCombs\Table\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use LaraCombs\Table\AbstractFilter;

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
        $value == 't' ? $this->trueValue = true : $this->falseValue = true;

        return $query->where($this->attribute, $value);
    }


    /**
     * Specify data that should be serialized to JSON for the filter.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'trueLabel' => $this->trueLabel,
            'falseLabel' => $this->falseLabel,
        ]);
    }
}
