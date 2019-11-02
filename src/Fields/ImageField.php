<?php

namespace PeteKlein\Performant\Fields;

use Carbon_Fields\Field;

class ImageField extends FieldBase
{
    /**
     * @inheritDoc
     */
    public function __construct(string $key, string $label, array $options = [], $defaultValue = null, bool $single = true)
    {
        parent::__construct($key, $label, 'image', $options, $defaultValue, $single);
    }

    /**
     * @inheritDoc
     */
    public function createAdminField()
    {
        return Field::make('image', $this->key, $this->label)
            ->set_value_type( 'url' );
    }
}
