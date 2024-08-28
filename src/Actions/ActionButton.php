<?php

namespace LaraCombs\Table\Actions;

use LaraCombs\Table\AbstractAction;
use LaraCombs\Table\Traits\CanCreateAnHttpRequestTrait;

/**
 * @method static static make(string $label)
 */
class ActionButton extends AbstractAction
{
    use CanCreateAnHttpRequestTrait;

    /**
     * Indicates if this action is a standalone action.
     */
    protected bool $standalone = true;

    /**
     * Determine if this action is dependent on a row.
     */
    public function dependentOnRow(bool $state): static
    {
        $this->dependentOnRow = $state;

        return $this;
    }
}
