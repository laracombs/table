<?php

namespace LaraCombs\Table\Traits;

use Illuminate\Http\Request;

trait HasSharedDataTrait
{
    /**
     * Specify additional data that should be serialized to JSON for the colum.
     */
    protected function sharedData(Request $request): array
    {
        return [];
    }
}
