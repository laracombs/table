<?php

namespace LaraCombs\Table\Traits;

trait MakeableTrait
{
    /**
     * Create a new column element.
     */
    public static function make(mixed ...$arguments): static
    {
        return new static(...$arguments);
    }
}
