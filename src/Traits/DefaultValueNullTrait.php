<?php

namespace LaraCombs\Table\Traits;

trait DefaultValueNullTrait
{
    /**
     * Resolve the default value.
     */
    protected function resolveDefaultValue(): mixed
    {
        return $this->default ?? null;
    }
}
