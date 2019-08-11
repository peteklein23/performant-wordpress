<?php

namespace PeteKlein\Performant\Fields;

abstract class FieldBase
{
    public $key;
    public $label;
    public $type;
    public $typeOptions;
    public $defaultValue;
    public $single;

    public function __construct(string $key, string $label, string $type, array $typeOptions = [], $defaultValue = null, bool $single = true)
    {
        $this->key = $key;
        $this->label = $label;
        $this->type = $type;
        $this->typeOptions = $typeOptions;
        $this->defaultValue = $defaultValue;
        $this->single = $single;
    }

    abstract public function createAdminField();
}
