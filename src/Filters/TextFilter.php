<?php

namespace LaraCombs\Table\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use LaraCombs\Table\AbstractFilter;
use LaraCombs\Table\Enums\TextFilterEnum;
use LaraCombs\Table\Exceptions\FilterException;

/**
 * @method static static make(string $label, string $attribute)
 */
class TextFilter extends AbstractFilter
{
    /**
     * The options for this filter.
     *
     * @var array<\LaraCombs\Table\Enums\TextFilterEnum>
     */
    protected array $options;

    /**
     * Create a new filter element.
     *
     * @param string  $label
     * @param string  $attribute
     */
    public function __construct(string $label, string $attribute)
    {
        $this->options = TextFilterEnum::cases();
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
        $case = TextFilterEnum::tryFrom($case);

        return match ($case) {
            TextFilterEnum::CONTAINS => $query->where(
                $this->attribute,
                $this->likeOperator($query),
                '%' . $value . '%'
            ),
            TextFilterEnum::NOT_CONTAINS => $query->where(
                $this->attribute,
                'NOT ' . $this->likeOperator($query),
                '%' . $value . '%'
            ),
            TextFilterEnum::EQUALS => $query->where($this->attribute, $value),
            TextFilterEnum::NOT_EQUALS => $query->where($this->attribute, '!=', $value),
            TextFilterEnum::STARTS_WITH => $query->where(
                $this->attribute,
                $this->likeOperator($query),
                '%' . $value
            ),
            TextFilterEnum::ENDS_WITH => $query->where(
                $this->attribute,
                $this->likeOperator($query),
                $value . '%'
            ),
            TextFilterEnum::NOT_STARTS_WITH => $query->where(
                $this->attribute,
                'NOT ' . $this->likeOperator($query),
                '%' . $value
            ),
            TextFilterEnum::NOT_ENDS_WITH => $query->where(
                $this->attribute,
                'NOT ' . $this->likeOperator($query),
                $value . '%'
            ),
            default => throw new FilterException(sprintf('Invalid case for %s.', __CLASS__))
        };
    }

    /**
     * Determine the options for this filter.
     *
     * @param array<\LaraCombs\Table\Enums\TextFilterEnum>  $options
     *
     * @return \LaraCombs\Table\Filters\TextFilter
     */
    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Specify additional data that should be serialized to JSON for the colum.
     */
    protected function sharedData(Request $request): array
    {
        return [
            'options' => Arr::mapWithKeys(
                $this->options,
                fn(TextFilterEnum $case) => [$case->value => $case->label()]
            ),
        ];
    }
}
