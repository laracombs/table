<?php

namespace LaraCombs\Table\Elements;

use Illuminate\Support\Str;
use LaraCombs\Table\AbstractElement;
use LaraCombs\Table\Traits\MakeableTrait;

/**
 * @method static static make(string $label, string $url, ?string $attribute = null)
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
     */
    public function __construct(string $label, string $url, ?string $attribute = null)
    {
        if (! $attribute) {
            $attribute = Str::kebab(class_basename(get_called_class()));
        }

        $this->sharedData = [
            'label' => $label,
            'url' => $url,
            'attribute' => $attribute,
            'target' => $this->target,
            'binding' => [
                'buttonClass' => [],
            ],
        ];
    }

    /**
     * Set the array of classes for Class binding for the button.
     *
     * @param array<string>|string $class
     * @return static
     */
    public function btnClass(array|string $class): static
    {
        $this->sharedData['binding']['buttonClass'] = (array) $class;

        return $this;
    }
}
