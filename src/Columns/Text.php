<?php

namespace LaraCombs\Table\Columns;

use LaraCombs\Table\AbstractColumn;
use LaraCombs\Table\Traits\HtmlRenderableTrait;
use LaraCombs\Table\Traits\IsSortableTrait;

/**
 * @method static static make(string $name, string $attribute, ?callable $resolveAttributeCallback = null)
 */
class Text extends AbstractColumn
{
    use HtmlRenderableTrait;
    use IsSortableTrait;
}
