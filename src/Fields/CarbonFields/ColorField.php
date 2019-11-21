<?php

namespace PeteKlein\Performant\Fields\CarbonFields;

use Carbon_Fields\Field;

class ColorField extends CFFieldBase
{
    private $colorOptions = [];
    private $alphaEnabled = true;

    public function __construct(string $key, string $label, $defaultValue = null, array $colorOptions = [], bool $alphaEnabled = true, array $options = [])
    {
        parent::__construct($key, $label, $defaultValue, $options);
        $this->colorOptions = $colorOptions;
        $this->alphaEnabled = $alphaEnabled;
    }

    /**
     * @inheritDoc
     */
    public function createAdminField() : \Carbon_Fields\Field\Field
    {
        $this->adminField = Field::make('color', $this->key, $this->label);
        $this->setAdminOptions();
        $this->setColorOptions();

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

        foreach ($this->options as $option => $value) {
            switch ($option) {
                case 'value_type':
                    $this->adminField->set_value_type($value);
                    break;
            }
        }
    }

    private function setColorOptions() : void
    {
        $this->adminField->set_palette($this->colorOptions);
        $this->adminField->set_alpha_enabled($this->alphaEnabled);
    }
}
