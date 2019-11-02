<?php

namespace PeteKlein\Performant\Fields;

abstract class FieldBase
{
    public $key;
    public $label;
    public $type;
    public $options;
    public $defaultValue;
    public $single;

    /**
     * Sets data for the field
     *
     * @param string $key - the meta key
     * @param string $label - the label to be shown in the WordPress admin
     * @param string $type - the type of field to be created
     * @param array $options - additional options to be used in field creation
     * @param mixed $defaultValue - the default value
     * @param boolean $single - whether the field is single or not
     */
    public function __construct(string $key, string $label, string $type, array $options = [], $defaultValue = null, bool $single = true)
    {
        $this->key = $key;
        $this->label = $label;
        $this->type = $type;
        $this->options = $options;
        $this->defaultValue = $defaultValue;
        $this->single = $single;
    }

    /**
     * Executes the code to create a field in the WordPress admin 
     */
    abstract public function createAdminField();
}
