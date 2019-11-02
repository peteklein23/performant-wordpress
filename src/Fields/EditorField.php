<?php

namespace PeteKlein\Performant\Fields;

use Carbon_Fields\Field;

class EditorField extends FieldBase
{
    /**
     * @inheritDoc
     */
    public function __construct(string $key, string $label, array $options = [], $defaultValue = null)
    {
        parent::__construct($key, $label, 'editor', $options, $defaultValue, true);
    }

    /**
     * @inheritDoc
     */
    public function createAdminField()
    {
        return Field::make('rich_text', $this->key, $this->label);
    }
}
