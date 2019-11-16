<?php

namespace PeteKlein\Performant\Fields\CarbonFields;

use Carbon_Fields\Toolset\Key_Toolset;
use PeteKlein\Performant\Fields\FieldBase;

abstract class CFFieldBase extends FieldBase
{
    public function __construct(string $key, string $label, array $options = [], $defaultValue = null)
    {
        parent::__construct($key, $label, $options, $defaultValue);
    }

    public function getPrefixedKey() {
        return Key_Toolset::KEY_PREFIX . $this->key;
    }

    /**
     * @inheritDoc
     */
    abstract public function createAdminField();

    /**
     * @inheritDoc
     */
    abstract public function getSelectionSQL() : string;

    /**
     * @inheritDoc
     */
    abstract public function getValue(array $meta);
}
