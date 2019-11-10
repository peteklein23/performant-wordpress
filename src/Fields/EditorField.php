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

    /**
     * @inheritDoc
     */
    public function getSelectionSQL()
    {
        return "= '$this->key'";
    }

    /**
     * @inheritDoc
     */
    public function getValue(array $meta)
    {
        foreach($meta as $m){
            if($m->meta_key === $this->key){
                if(!empty($m->meta_value)) {
                    return $m->meta_value;
                }
            }
        }

        return $this->defaultValue;
    }
}
