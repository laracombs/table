<?php

namespace LaraCombs\Table\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasResourceTrait
{
    /**
     * The resource associated with the column.
     */
    public Model $resource;

    public function forResource(Model $resource): static
    {
        $this->resource = $resource;

        return $this;
    }
}
