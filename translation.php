<?php

/**
 * @Todo Multilanguage?
 */

$items = json_decode(file_get_contents(__DIR__ . '/lang/de.json'), true);
ksort($items, SORT_STRING | SORT_FLAG_CASE);

file_put_contents(
    __DIR__ . '/lang/de.json',
    json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
);


$en = json_decode(file_get_contents(__DIR__ . '/lang/en.json'), true);
$enItems = [];
foreach ($items as $key => $value) {
    $enItems[$key] = ! empty($en[$key]) ? $en[$key] : $key;
}

file_put_contents(
    __DIR__ . '/lang/en.json',
    json_encode($enItems, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
);

//$class = '<?php
//
//namespace LaraCombs\Table\Support;
//
//class TranslationData
//{
//    public function __invoke(): array
//    {
//        return [
//';
//
//foreach ($items as $item) {
//    $class .= '            ';
//    $class .= "'$item',\n";
//}
//
//$class .= '        ];
//    }
//}
//';
//
//file_put_contents(__DIR__ . '/src/Support/TranslationData.php', $class);
