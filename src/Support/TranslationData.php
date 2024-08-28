<?php

namespace LaraCombs\Table\Support;

use Illuminate\Support\Arr;

class TranslationData
{
    /**
     * @todo optimize.
     */
    public function __invoke(): array
    {
        return Arr::only(
            app('translator')
                ->getLoader()
                ->load(app()->getLocale(), '*', '*'),
            array_keys(
                json_decode(file_get_contents(__DIR__ . '/../../lang/en.json'), true)
            )
        ) + [
            'pagination' => [
                'next' => __('pagination.next'),
                'previous' => __('pagination.previous'),
            ],
            ];
    }
}
