<?php

namespace LaraCombs\Table\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait HasUriKeyTrait
{
    /**
     * The URI key for the given element.
     */
    public ?string $uriKey = null;

    /**
     * Resolve the URI key for the given element.
     */
    protected function resolveUriKey(Request $request): void
    {
        $this->uriKey = empty($this->uriKey) ? $this->uriKeyFallback($request) : trim(Str::lower($this->uriKey));
    }

    /**
     * Return a default URI key as fallback for this element.
     */
    protected function uriKeyFallback(Request $request): string
    {
        return Str::snake(class_basename(get_called_class()));
    }
}
