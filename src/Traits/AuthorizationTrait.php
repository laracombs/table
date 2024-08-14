<?php

namespace LaraCombs\Table\Traits;

use Closure;
use Illuminate\Http\Request;

trait AuthorizationTrait
{
    /**
     * The callback to authorize viewing the column.
     */
    public ?Closure $authorizeCallback = null;

    /**
     * Determine if the column is available for the current request.
     */
    public function authorize(Request $request): bool
    {
        return $this->authorizeCallback ? call_user_func($this->authorizeCallback, $request) : true;
    }

    /**
     * Set the callback to authorize viewing the column.
     *
     * @param  \Closure(\Illuminate\Http\Request):bool  $callback
     * @return $this
     */
    public function canSee(Closure $callback): static
    {
        $this->authorizeCallback = $callback;

        return $this;
    }
}
