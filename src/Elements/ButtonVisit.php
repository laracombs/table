<?php

namespace LaraCombs\Table\Elements;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use LaraCombs\Table\AbstractElement;
use LaraCombs\Table\Traits\MakeableTrait;

/**
 * @method static static make(string|callable $label, string|callable $url, ?string $attribute = null)
 */
class ButtonVisit extends AbstractElement
{
    use MakeableTrait;

    /**
     * Specifies where to open the linked document.
     */
    protected ?string $target = null;

    /**
     * Initialize a new Element class.
     *
     * @param  string|(callable(\Illuminate\Database\Eloquent\Model, \Illuminate\Http\Request):(string))  $label
     * @param  string|(callable(\Illuminate\Database\Eloquent\Model, \Illuminate\Http\Request):(string))  $url
     * @param  string|null  $attribute
     */
    public function __construct(string|callable $label, string|callable $url, ?string $attribute = null)
    {
        if (! $attribute) {
            $attribute = Str::kebab(class_basename(get_called_class()));
        }

        $this->sharedData = [
            'label' => $label,
            'url' => $url,
            'attribute' => $attribute,
            'target' => $this->target,
        ];
    }

    /**
     * Specify data that should be serialized to JSON for the colum.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $request = app(Request::class);

        $callables = ['label', 'url'];
        foreach ($callables as $callable) {
            if (is_callable($this->sharedData[$callable])) {
                $this->sharedData[$callable] = call_user_func($this->sharedData[$callable], $this->resource, $request);
            }
        }

        return parent::jsonSerialize();
    }
}
