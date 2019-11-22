<?php

namespace PeteKlein\Performant\Fields\CarbonFields;

use Carbon_Fields\Field;

// TODO: standardize output with Group
class MultiSelectField extends CFFieldBase
{
    private $selectOptions = [];

    public function __construct(string $key, string $label, $defaultValue = null, array $selectOptions = [], array $options = [])
    {
        parent::__construct($key, $label, $defaultValue, $options);
        $this->selectOptions = $selectOptions;
    }

    /**
     * @inheritDoc
     */
    public function createAdminField() : \Carbon_Fields\Field\Field
    {
        $this->adminField = Field::make('set', $this->key, $this->label);
        $this->setAdminOptions();
        $this->setSelectOptions();

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
    }

    private function setSelectOptions() : void
    {
        $this->adminField->set_options($this->selectOptions);
    }
}
