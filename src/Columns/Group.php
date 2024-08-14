<?php

namespace LaraCombs\Table\Columns;

use Illuminate\Http\Request;
use LaraCombs\Table\AbstractColumn;
use LaraCombs\Table\AbstractElement;

/**
 * @method static static make(string $name, ?string $attribute = null)
 */
class Group extends AbstractColumn
{
    /**
     * The array of items.
     *
     * @var array<\LaraCombs\Table\AbstractElement>
     */
    protected array $items = [];

    /**
     * Create a new column element.
     */
    public function __construct(string $name, ?string $attribute = null)
    {
        if (! $attribute) {
            $attribute = 'group';
        }

        parent::__construct($name, $attribute);
    }

    /**
     * Set the array of items.
     *
     * @return $this
     */
    public function items(array $items): static
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Add an item to the array of items.
     *
     * @return $this
     */
    public function addItem(AbstractElement $item): static
    {
        return $this;
    }

    /**
     * Resolve the column value for display.
     */
    protected function resolveValue(Request $request): array
    {
        return $this->items;
    }
}
