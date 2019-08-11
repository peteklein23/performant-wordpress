<?php

namespace PeteKlein\Performant\Fields;

class Field
{
    const TYPE_NUMBER = 'number';
    const TYPE_TEXT = 'text';

    public static function create(string $key, string $label, string $type, array $typeOptions = [], $defaultValue = null, bool $single = true): FieldBase
    {
        if ($type === self::TYPE_NUMBER) {
            return new NumberField($key, $label, $type, $typeOptions = [], $defaultValue = null, $single = true);
        }
        if ($type === self::TYPE_NUMBER) {
            return new TextField($key, $label, $type, $typeOptions = [], $defaultValue = null, $single = true);
        }
    }
}
