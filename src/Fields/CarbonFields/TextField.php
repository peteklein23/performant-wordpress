<?php

namespace PeteKlein\Performant\Fields\CarbonFields;

use Carbon_Fields\Field;

class TextField extends CFFieldBase
{
    public function __construct(string $key, string $label, $defaultValue = null, array $options = [])
    {
        parent::__construct($key, $label, $defaultValue, $options);
    }

    /**
     * @inheritDoc
     */
    public function createAdminField() : \Carbon_Fields\Field\Field
    {
        $this->adminField = Field::make('text', $this->key, $this->label);
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
        $allowedAttributes = ['min', 'max', 'type', 'maxLength', 'pattern', 'placeholder', 'readOnly'];
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
