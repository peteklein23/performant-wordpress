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
