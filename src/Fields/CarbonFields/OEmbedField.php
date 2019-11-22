<?php

namespace PeteKlein\Performant\Fields\CarbonFields;

use Carbon_Fields\Field;
use Embed\Embed;

// TODO: determine if I want to keep this, and if so, how to do it outside of WordPress
class OEmbedField extends CFFieldBase
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
        $this->adminField = Field::make('oembed', $this->key, $this->label);
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
                return Embed::create($m->meta_value);
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
}
