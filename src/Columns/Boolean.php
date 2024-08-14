<?php

namespace LaraCombs\Table\Columns;

use LaraCombs\Table\AbstractColumn;
use LaraCombs\Table\Traits\IsSortableTrait;

/**
 * @method static static make(string $name, string $attribute, ?callable $resolveAttributeCallback = null)
 */
class Boolean extends AbstractColumn
{
    use IsSortableTrait;
}
