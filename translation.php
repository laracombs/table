<?php

/**
 * @Todo Multilanguage?
 */

$items = json_decode(file_get_contents(__DIR__ . '/lang/de.json'), true);
ksort($items);

file_put_contents(
    __DIR__ . '/lang/de.json',
    json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
);

$items = array_combine(array_keys($items), array_keys($items));

file_put_contents(
    __DIR__ . '/lang/en.json',
    json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
);

$class = '<?php

namespace LaraCombs\Table\Support;

class TranslationData
{
    public function __invoke(): array
    {
        return [
';

foreach ($items as $item) {
    $class .= '            ';
    $class .= "'$item' => __('$item'),\n";
}

$class .= '        ];
    }
}
';

file_put_contents(__DIR__ . '/src/Support/TranslationData.php', $class);
