<?php

namespace LaraCombs\Table\Traits;

use Illuminate\Http\Request;

trait HasComponentTrait
{
    /**
     * The frontend component for the element.
     *
     * @note The parent class 'basename' of the parent class if null.
     */
    public ?string $component = null;

    /**
     * Get the frontend component for the element.
     */
    public function component(Request $request): string
    {
        return $this->component ?: $this->defaultComponent($request);
    }

    protected function defaultComponent(Request $request): string
    {
        $component = 'LaracombsTable' . class_basename(get_called_class());

        if (! str_ends_with($component, $this->type)) {
            $component .= $this->type;
        }

        return $component;
    }
}
