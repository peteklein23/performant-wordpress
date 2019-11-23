<?php

namespace PeteKlein\Performant\Fields;

abstract class FieldGroupBase {

    private $name;
    private $fields = [];

    /**
     * Initialize the field group, usually by calling `$this->setFields`
     *
     * @return void
     */
    public function __construct(string $name, array $fields)
    {
        $this->name = $name;
        $this->setFields($fields);
    }

    public function getName() : string
    {
        return $this->name;
    }

    private function addField(FieldBase $field) : void
    {
        $this->fields[] = $field;
    }

    private function setFields(array $fields) : void
    {
        $this->fields = [];

        foreach ($fields as $field) {
            $this->addField($field);
        }
    }

    public function listFields() : array
    {
        return $this->fields;
    }
}
