<?php

namespace LaraCombs\Table\Elements;

class ButtonLink extends ButtonVisit
{
    /**
     * Opens the linked document in a new window or tab.
     *
     * @return $this
     */
    public function openInNewTab(): static
    {
        $this->sharedData['target'] = '_blank';

        return $this;
    }
}
