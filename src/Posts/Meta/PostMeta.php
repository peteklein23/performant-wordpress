<?php

namespace PeteKlein\Performant\Posts\Meta;

use PeteKlein\Performant\Fields\FieldBase;

class PostMeta
{
    public $postId;
    private $values = [];

    public function __construct(int $postId)
    {
        $this->postId = $postId;
    }

    public function add($key, $value){
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
