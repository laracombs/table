<?php

namespace LaraCombs\Table\Events;

use Illuminate\Foundation\Events\Dispatchable;
use LaraCombs\Table\AbstractTable;

/**
 * @method static mixed dispatch(\LaraCombs\Table\AbstractTable $table)
 */
class TableCreated
{
    use Dispatchable;

    /**
     * The Table instance.
     *
     * @var \LaraCombs\Table\AbstractTable
     */
    public AbstractTable $table;

    /**
     * Create a new event instance.
     */
    public function __construct(AbstractTable $table)
    {
        $this->table = $table;
    }
}
