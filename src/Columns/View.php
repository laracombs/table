<?php

namespace LaraCombs\Table\Columns;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use LaraCombs\Table\AbstractColumn;
use LaraCombs\Table\Exceptions\ColumnException;
use LaraCombs\Table\Traits\HtmlRenderableTrait;
use LaraCombs\Table\Traits\IsSortableTrait;

/**
 * @method static static make(string $name, string $attribute, ?callable $resolveAttributeCallback = null)
 */
class View extends AbstractColumn
{
    use HtmlRenderableTrait;
    use IsSortableTrait;

    /**
     * The Blade view that should be rendered for the column.
     */
    protected string $view;

    /**
     * The data for the Blade view that should be rendered for the column.
     */
    protected array $viewData;

    /**
     * The merge data for the Blade view that should be rendered for the column.
     */
    protected array $viewMergeData;

    /**
     * The GitHub Flavored Markdown options.
     */
    protected array $markdownOptions;

    /**
     * The GitHub Flavored Markdown extensions.
     */
    protected array $markdownExtensions;

    /**
     * Determine if the column should render as Markdown.
     */
    protected bool $isMarkdown = false;

    /**
     * Create a new column element.
     *
     * @param  (callable(mixed, mixed, ?string):(mixed))|null  $resolveAttributeCallback
     */
    public function __construct(string $name, string $attribute, ?callable $resolveAttributeCallback = null)
    {
        $this->asHtml();

        parent::__construct($name, $attribute, $resolveAttributeCallback);
    }

    /**
     * Render Blade view as Markdown.
     */
    public function asMarkdown(array $markdownOptions = [], array $markdownExtensions = []): static
    {
        $this->isMarkdown = true;
        $this->markdownOptions = $markdownOptions;
        $this->markdownExtensions = $markdownExtensions;

        return $this;
    }

    /**
     * Set the Blade view.
     *
     * @return $this
     */
    public function view(string $view, array $data = [], array $mergeData = []): static
    {
        $this->view = $view;
        $this->viewData = $data;
        $this->viewMergeData = $mergeData;

        return $this;
    }

    /**
     * Resolve the column value for display.
     *
     * @throws \LaraCombs\Table\Exceptions\ColumnException
     * @throws \Throwable
     */
    protected function resolveValue(Request $request): string
    {
        if (empty($this->view)) {
            throw new ColumnException(
                sprintf('View not defined for %s.', $this->attribute)
            );
        }

        $rendered = view($this->view, $this->viewData, $this->viewMergeData)->render();

        if ($this->isMarkdown) {
            return Str::markdown($rendered, $this->markdownOptions, $this->markdownExtensions);
        }

        return $rendered;
    }
}
