<?php

namespace LaraCombs\Table\Enums;

enum TextFilterEnum: int
{
    case CONTAINS = 1;
    case NOT_CONTAINS = 2;
    case EQUALS = 3;
    case NOT_EQUALS = 4;
    case STARTS_WITH = 5;
    case ENDS_WITH = 6;
    case NOT_STARTS_WITH = 7;
    case NOT_ENDS_WITH = 8;

    /**
     * Get the translated label for the given case.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::CONTAINS => __('Contains'),
            self::NOT_CONTAINS => __('Does not contains'),
            self::EQUALS => __('Equals'),
            self::NOT_EQUALS => __('Does not equals'),
            self::STARTS_WITH => __('Starts with'),
            self::ENDS_WITH => __('Ends with'),
            self::NOT_STARTS_WITH => __('Does not starts with'),
            self::NOT_ENDS_WITH => __('Does not Ends with'),
        };
    }
}
