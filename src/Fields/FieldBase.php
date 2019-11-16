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
     */
    public function __construct(string $key, string $label, array $options = [], $defaultValue = null)
    {
        $this->key = $key;
        $this->label = $label;
        $this->options = $options;
        $this->defaultValue = $defaultValue;
    }

    /**
     * Executes the code to create a field in the WordPress admin 
     */
    abstract public function createAdminField();

    /**
     * Returns a piece of SQL to use in the WHERE clause: e.g. `"= '$this->key'"` OR `"LIKE '%$this->key%'"`
     */
    abstract public function getSelectionSQL() : string;

    /**
     * Returns the formatted value from meta results
     * 
     * @param string $result - meta results for a given object
     */
    abstract public function getValue(array $meta);
}
