<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Value
    |--------------------------------------------------------------------------
    |
    | This value is used as the default if the requested attribute of the
    | current object returns a null value.
    |
    */

    'default_value' => '—',

    /*
    |--------------------------------------------------------------------------
    | Search Debounce
    |--------------------------------------------------------------------------
    |
    | This value is used to determine default debounce amount in seconds for
    | table search requests.
    |
    */

    'search_debounce' => 0.5,

    /*
    |--------------------------------------------------------------------------
    | Pagination Type
    |--------------------------------------------------------------------------
    |
    | This options defines the default pagination style type.
    | Available: links
    | Upcoming: simple
    |
    */

    'pagination' => 'links',

    /*
    |--------------------------------------------------------------------------
    | Per Page
    |--------------------------------------------------------------------------
    |
    | The default per page pagination options.
    |
    */

    'per_page' => [20, 50, 100],

    /*
    |--------------------------------------------------------------------------
    | Elements Default Classes
    |--------------------------------------------------------------------------
    |
    | The default class for each element.
    |
    */

    'classes' => [
        'table' => [
            'container' => 'lc-table-container',
            'header' => 'lc-table-header',
            'search' => [
                'container' => 'lc-table-search',
                'input' => 'lc-table-input',
            ],
            'toolbar' => [
                'container' => 'lc-table-toolbar',
            ],
            'bottom_toolbar' => 'lc-table-bottom-toolbar',
            'pagination' => [
                'container' => 'pagination',
                'button' => 'pagination-btn',
                'button_disabled' => 'pagination-btn-disabled',
            ],
        ],
        'button' => 'lc-table-button',
        'dropdown' => [
            'items' => 'lc-table-dropdown-item',
        ],
        'filters' => [
            'container' => 'lc-filters-container',
        ],
    ],
];
