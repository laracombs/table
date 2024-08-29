<?php

namespace LaraCombs\Table\Columns\JavaScript;

use Illuminate\Http\Request;
use LaraCombs\Table\AbstractColumn;
use LaraCombs\Table\Traits\DefaultValueNullTrait;

/**
 * @method static static make(string $name, string $attribute, ?callable $resolveAttributeCallback = null)
 */
class DateToLocaleString extends AbstractColumn
{
    use DefaultValueNullTrait;

    /**
     * The locale for this field.
     */
    protected ?string $locale = null;

    /**
     * Determine the locale for this field.
     */
    public function locale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    protected function resolveLocale(Request $request): string
    {
        return $this->locale ?: app()->getLocale();
    }

    /**
     * Specify additional data that should be serialized to JSON for the colum.
     */
    protected function sharedData(Request $request): array
    {
        return [
            'locale' => $this->resolveLocale($request),
        ];
    }
}
