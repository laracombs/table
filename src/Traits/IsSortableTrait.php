<?php

namespace LaraCombs\Table\Traits;

trait IsSortableTrait
{
    /**
     * Determine if the column is sortable.
     */
    public bool $sortable = true;

    /**
     * Set the column as sortable or not.
     *
     * @return $this
     */
    public function sortable(bool $value = true): static
    {
        $this->sortable = $value;

        return $this;
    }
}
