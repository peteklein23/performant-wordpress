<?php

namespace PeteKlein\Performant\Fields\CarbonFields;

use Carbon_Fields\Field;

class DateTimeField extends CFFieldBase
{
    private $storageFormat;
    private $pickerOptions;

    /**
     * Undocumented function
     *
     * @param string $key
     * @param string $label
     * @param [type] $defaultValue
     * @param string $storageFormat
     * @param array $pickerOptions - @see https://flatpickr.js.org/options/
     * @param array $options
     */

     // TODO: implement set_input_format - https://docs.carbonfields.net/#/fields/date
    public function __construct(
        string $key, 
        string $label, 
        $defaultValue = null, 
        string $storageFormat = 'Y-m-d H:i:s',
        array $pickerOptions = [], 
        array $options = []
    ) {
        parent::__construct($key, $label, $defaultValue, $options);
        $this->storageFormat = $storageFormat;
        $this->pickerOptions = $pickerOptions;
    }

    /**
     * @inheritDoc
     */
    public function createAdminField() : \Carbon_Fields\Field\Field
    {
        $this->adminField = Field::make('date_time', $this->key, $this->label);
        $this->setAdminOptions();

        return $this->adminField;
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

    /**
     * @inheritDoc
     */
    public function setAdminOptions() : void
    {
        $this->setDefaultAdminOptions();
        $this->adminField->set_storage_format($this->storageFormat);
        $this->adminField->set_picker_options($this->pickerOptions);
        $this->verifyAttributes();

        foreach ($this->options as $option => $value) {
            switch ($option) {
                case 'value_type':
                    $this->adminField->set_value_type($value);
                    break;
            }
        }
    }

    private function verifyAttributes()
    {
        $allowedAttributes = ['placeholder', 'readOnly'];
        if(!empty($this->options['attributes'])) {
            $attributeKeys = array_keys($this->options['attributes']);
            foreach ($attributeKeys as $key) {
                if (!in_array($key, $allowedAttributes)) {
                    throw new \Exception("Attribute $key is not allowed.");
                }
            }
        }
    }
}
