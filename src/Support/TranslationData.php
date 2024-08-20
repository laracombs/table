<?php

namespace LaraCombs\Table\Support;

class TranslationData
{
    public function __invoke(): array
    {
        return [
            'Actions' => __('Actions'),
            'Apply' => __('Apply'),
            'Ascending' => __('Ascending'),
            'Contains' => __('Contains'),
            'Descending' => __('Descending'),
            'Does not Ends with' => __('Does not Ends with'),
            'Does not contains' => __('Does not contains'),
            'Does not equals' => __('Does not equals'),
            'Does not starts with' => __('Does not starts with'),
            'Ends with' => __('Ends with'),
            'Equals' => __('Equals'),
            'Filters' => __('Filters'),
            'No' => __('No'),
            'No resources were found.' => __('No resources were found.'),
            'Order by „:name“' => __('Order by „:name“'),
            'Remove' => __('Remove'),
            'Search' => __('Search'),
            'Starts with' => __('Starts with'),
            'Yes' => __('Yes'),
        ];
    }
}
