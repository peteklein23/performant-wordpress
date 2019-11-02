<?php

namespace PeteKlein\Performant\Fields;

use Carbon_Fields\Field;

class NumberField extends FieldBase
{
    public function __construct(string $key, string $label, array $typeOptions = [], $defaultValue = null, bool $single = true)
    {
        parent::__construct($key, $label, 'number', $typeOptions, $defaultValue, $single);
    }

    public function createAdminField()
    {
        return Field::make('text', $this->key, $this->label)
            ->set_attribute('type', 'number')
            ->set_attribute('min', 0)
            ->set_attribute('max', 100)
            ->set_attribute('step', 1)
            ->set_required(true)
            ->set_default_value($this->defaultValue);
    }
}
