<?php

namespace LaraCombs\Table\Support;

class TranslationData
{
    public function __invoke(): array
    {
        return [
            'Actions' => __('Actions'),
            'Ascending' => __('Ascending'),
            'Descending' => __('Descending'),
            'Filters' => __('Filters'),
            'Order by „:name“' => __('Order by „:name“'),
            'Search' => __('Search'),
        ];
    }
}
