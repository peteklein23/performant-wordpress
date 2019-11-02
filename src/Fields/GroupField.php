<?php

namespace PeteKlein\Performant\Fields;

use Carbon_Fields\Field;

class GroupField extends FieldBase
{
    /**
     * @inheritDoc
     */
    public function __construct(string $key, string $label, array $options = [], $defaultValue = null, bool $single = true)
    {
        parent::__construct($key, $label, 'group', $options, $defaultValue, $single);
    }

    /**
     * @inheritDoc
     */
    public function createAdminField()
    {
        return Field::make('complex', $this->key, $this->label)
            // ->set_layout('tabbed-horizontal')
            ->add_fields($this->getAdminFields());
    }

    /**
     * Return created admin fields
     *
     * @return array admin fields
     */
    public function getAdminFields() : array {
        $subFields = [];
        foreach($this->options['fields'] as $field){
            $subFields[] = $field->createAdminField();
        }

        return $subFields;
    }
}
