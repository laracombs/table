<?php

namespace LaraCombs\Table\Actions;

use LaraCombs\Table\AbstractAction;

/**
 * @method static static make(string $label)
 */
class ActionButton extends AbstractAction
{
    /**
     * @param bool  $state
     *
     * @return $this
     */
    public function dependentOnRow(bool $state): static
    {
        return $this;
    }

    /**
     * Indicates if this action is a standalone action.
     */
    protected bool $standalone = true;
}
