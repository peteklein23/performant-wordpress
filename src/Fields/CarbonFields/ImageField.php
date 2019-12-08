<?php

namespace PeteKlein\Performant\Fields\CarbonFields;

use Carbon_Fields\Field;

class ImageField extends CFFieldBase
{
    public function __construct(
        string $key, 
        string $label, 
        $defaultValue = null,
        array $options = []
    ) {
        $fieldDefaults = [
            'value_type' => 'url'
        ];
        $combinedOptions = $this->combineOptions($fieldDefaults, $options);

        parent::__construct($key, $label, $defaultValue, $combinedOptions);
    }

    /**
     * @inheritDoc
     */
    public function createAdmin() : void
    {
        $this->adminField = Field::make('image', $this->key, $this->label);
        $this->setSharedOptions();

        foreach ($this->options as $option => $value) {
            switch ($option) {
                case 'value_type':
                    $this->adminField->set_value_type($value);
                    break;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getSelectionSQL() : string
    {
        $metaKey = $this->getPrefixedKey();

        return "= '$metaKey'";
    }

    /**
     * @inheritDoc
     */
    public function getValue(array $meta)
    {
        foreach ($meta as $m) {
            if ($m->meta_key === $this->getPrefixedKey() && $m->meta_value) {
                return $m->meta_value;
            }
        }

        return $this->defaultValue;
    }
}
