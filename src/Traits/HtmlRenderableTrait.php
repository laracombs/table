<?php

namespace LaraCombs\Table\Traits;

trait HtmlRenderableTrait
{
    /**
     * Determine if the column should render as HTML.
     */
    public bool $renderHtml = false;

    /**
     * Set the column to render as HTML.
     *
     * @return $this
     */
    public function asHtml(bool $value = true): static
    {
        $this->renderHtml = $value;

        return $this;
    }
}
