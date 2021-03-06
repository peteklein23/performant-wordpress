<?php

namespace PeteKlein\Performant\Fields\CarbonFields;

use Carbon_Fields\Field;

class MultiSelectField extends CFFieldBase
{
    public function __construct(
        string $key, 
        string $label, 
        $defaultValue = null,
        array $options = []
    ) {
        $fieldDefaults = [
            'options' => []
        ];
        $combinedOptions = $this->combineOptions($fieldDefaults, $options);

        parent::__construct($key, $label, $defaultValue, $combinedOptions);
    }

    /**
     * @inheritDoc
     */
    public function createAdmin() : void
    {
        $this->adminField = Field::make('multiselect', $this->key, $this->label);
        $this->setSharedOptions();

        foreach ($this->options as $option => $value) {
            switch ($option) {
                case 'options':
                    $this->adminField->set_options($value);
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
