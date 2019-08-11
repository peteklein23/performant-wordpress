<?php

namespace PeteKlein\Performant\Fields;

use Carbon_Fields\Field;

class TextField extends FieldBase
{
    public function __construct(string $key, string $label, string $type, array $typeOptions = [], $defaultValue = null, bool $single = true)
    {
        parent::__construct($key, $label, $type, $typeOptions, $defaultValue, $single);
    }

    public function createAdminField()
    {
        return Field::make('text', $this->key, $this->label);
    }
}
