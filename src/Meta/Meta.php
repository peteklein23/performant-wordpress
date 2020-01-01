<?php

namespace PeteKlein\Performant\Meta;

class Meta
{
    public $id;
    private $values = [];

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function add($key, $value)
    {
        $this->values[$key] = $value;
    }

    public function get(string $key)
    {
        if (!empty($this->values[$key])) {
            return $this->values[$key];
        }

        return null;
    }

    public function list()
    {
        return $this->values;
    }
}
