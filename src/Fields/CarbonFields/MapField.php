<?php

namespace PeteKlein\Performant\Fields\CarbonFields;

use Carbon_Fields\Field;

class MapField extends CFFieldBase
{
    const DEFAULT_OPTIONS = [
        'latitude' => 0,
        'longitude' => 0,
        'zoom' => 0
    ];

    public function __construct(string $key, string $label, $defaultValue = null, array $options = [])
    {
        $mergedOptions = array_merge(self::DEFAULT_OPTIONS, $options);
        parent::__construct($key, $label, $defaultValue, $mergedOptions);
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
    public function createAdminField() : \Carbon_Fields\Field\Field
    {
        $this->adminField =  Field::make('map', $this->key, $this->label);
        $this->setAdminOptions();
        
        return $this->adminField;
    }

    /**
     * @inheritDoc
     */
    public function setAdminOptions() : void
    {
        $this->setDefaultAdminOptions();

        $latitude = $this->options['latitude'];
        $longitude = $this->options['longitude'];
        $zoom = $this->options['zoom'];

        $this->adminField->set_position($latitude, $longitude, $zoom);
    }
}
