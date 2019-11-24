<?php

namespace PeteKlein\Performant\Fields\CarbonFields;

use Carbon_Fields\Container;
use PeteKlein\Performant\Fields\FieldGroupBase;
use PeteKlein\Performant\Fields\CarbonFields\CFFieldBase;

class CFContainer
{
    private $container;

    public function __construct(FieldGroupBase $fieldGroup, string $table, string $type, string $value)
    {
        $this->container = Container::make($table, $fieldGroup->getName())
            ->where($type, '=', $value);

        $this->setFields($fieldGroup->listFields());
    }

    private function setFields(array $fields)
    {
        foreach ($fields as $field) {
            $this->addAdminField($field);
        }
    }

    public function addAdminField(CFFieldBase $field)
    {
        $field->createAdmin();
        $this->container->add_fields([$field->getAdminField()]);

        return $this;
    }
}
