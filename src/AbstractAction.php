<?php

namespace LaraCombs\Table;

use Illuminate\Http\Request;
use Illuminate\Support\Traits\Macroable;
use JsonSerializable;
use LaraCombs\Table\Traits\AuthorizationTrait;
use LaraCombs\Table\Traits\HasComponentTrait;
use LaraCombs\Table\Traits\MakeableTrait;

abstract class AbstractAction implements JsonSerializable
{
    use AuthorizationTrait;
    use Macroable;
    use HasComponentTrait;
    use MakeableTrait;

    /**
     * The LaraCombs Table Element Type.
     */
    protected string $type = 'Action';

    /**
     * Indicates if this action is dependent on a row.
     */
    protected bool $dependentOnRow = true;

    /**
     * Indicates if this action is a standalone action.
     */
    protected bool $standalone = false;

    /**
     * The label for this action.
     */
    protected string $label;

    /**
     * Create a new action element.
     */
    public function __construct(string $label)
    {
        $this->label = $label;
    }

    /**
     * Resolve the label of this action.
     */
    protected function resolveLabel(Request $request): bool
    {
        return $this->dependentOnRow;
    }

    /**
     * Resolve if this action is dependent on a row.
     */
    protected function resolveDependentOnRow(Request $request): bool
    {
        return $this->dependentOnRow;
    }

    /**
     * Determine if this action is a standalone action.
     */
    public function isStandalone(Request $request): bool
    {
        return $this->standalone;
    }

    /**
     * Specify additional data that should be serialized to JSON for the colum.
     */
    protected function sharedData(Request $request): array
    {
        return [];
    }

    /**
     * Specify data that should be serialized to JSON for the colum.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $request = app(Request::class);

        return array_merge([
                'component' => $this->component($request),
                'dependentOnRow' => $this->resolveDependentOnRow($request),
            ], $this->sharedData($request));
    }
}
