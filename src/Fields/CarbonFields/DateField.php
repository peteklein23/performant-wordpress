<?php

namespace PeteKlein\Performant\Fields\CarbonFields;

use Carbon_Fields\Field;

class DateField extends CFFieldBase
{
    // TODO: implement set_input_format - https://docs.carbonfields.net/#/fields/date
    public function __construct(
        string $key, 
        string $label, 
        $defaultValue = null,
        array $options = []
    ) {
        $fieldDefaults = [
            'storage_format' => 'Y-m-d',
            'picker_options' => []
        ];
        $combinedOptions = $this->combineOptions($fieldDefaults, $options);

        parent::__construct($key, $label, $defaultValue, $combinedOptions);
    }

    /**
     * @inheritDoc
     */
    public function createAdmin() : void
    {
        $this->adminField = Field::make('date', $this->key, $this->label);
        $this->setSharedOptions();

        foreach ($this->options as $option => $value) {
            switch ($option) {
                case 'storage_format':
                    $this->adminField->set_storage_format($value);
                    break;
                case 'picker_options':
                    $this->adminField->set_picker_options($value);
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
