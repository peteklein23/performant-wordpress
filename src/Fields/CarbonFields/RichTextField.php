<?php

namespace PeteKlein\Performant\Fields\CarbonFields;

use Carbon_Fields\Field;

class RichTextField extends CFFieldBase
{
    public function __construct(
        string $key, 
        string $label, 
        $defaultValue = null,
        array $options = []
    ) {
        parent::__construct($key, $label, $defaultValue, $options);
    }

    /**
     * @inheritDoc
     */
    public function createAdmin() : void
    {
        $this->adminField = Field::make('rich_text', $this->key, $this->label);
        $this->setSharedOptions();
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
