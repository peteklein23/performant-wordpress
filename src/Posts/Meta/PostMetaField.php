<?php

namespace PeteKlein\Performant\Posts\Meta;

use Carbon_Fields\Field;

class PostMetaField
{
    const TYPE_OPTIONS = [
        'bool' => [],
        'color' => [],
        'date' => [],
        'dateTime' => [],
        'file' => [],
        'html' => [],
        'image' => [],
        'selectOne' => [],
        'selectMany' => [],
        'text' => [],
        'textarea' => [],
        'time' => []
    ];

    public $key;
    public $label;
    public $type;
    public $typeOptions;
    public $defaultValue;
    public $single;

    public function __construct(string $key, string $label, string $type, array $typeOptions = [], $defaultValue = null, bool $single = true)
    {
        $types = array_keys(self::TYPE_OPTIONS);
        if (!in_array($type, $types)) {
            throw new \Exception('Unknown field type');
        }

        $this->key = $key;
        $this->label = $label;
        $this->type = $type;
        $this->typeOptions = $typeOptions;
        $this->defaultValue = $defaultValue;
        $this->single = $single;
    }

    public function getAdminField()
    {
        if ($this->type === 'text') {
            return Field::make('text', $this->key, $this->label);
        };
    }
}
