<?php

namespace LaraCombs\Table\Traits;

trait HasClassAndStyleBindingTrait
{
    /**
     * The Class and Style Bindings.
     *
     * @var array<string, array<string, mixed>>
     */
    protected array $bindings = [
        'classes' => [],
        'styles' => [],
    ];

    /**
     * Add Class binding.
     *
     * @param  array<string>|string  $class
     * @return static
     */
    protected function class(array|string $class): static
    {
        $this->bindings['classes'] = array_unique(array_merge($this->bindings['classes'], (array) $class));

        return $this;
    }

    /**
     * Add Style binding.
     *
     * @param  array<string>|string  $class
     * @return static
     */
    protected function style(array|string $class): static
    {
        $this->bindings['styles'] = array_unique(array_merge($this->bindings['styles'], (array) $class));

        return $this;
    }
}
