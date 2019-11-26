<?php

namespace PeteKlein\Performant\Fields\CarbonFields;

use Carbon_Fields\Field;

class GroupField extends CFFieldBase
{
    private $fields = [];

    /**
     * @inheritDoc
     */
    public function __construct(string $key, string $label, $defaultValue = null, array $fields, array $options = [])
    {
        $fieldDefaults = [
            'duplicate_groups_allowed' => false,
            'layout' => 'grid',
            'collapsed' => false,
            'min' => -1,
            'max' => -1,
            'labels' => [
                'singular_name' => 'Entry',
                'plural_name' => 'Entries'
            ]
        ];
        $combinedOptions = $this->combineOptions($fieldDefaults, $options);
        
        parent::__construct($key, $label, $defaultValue, $combinedOptions);
        $this->setFields($fields);
    }

    public function getSelectionSQL() : string
    {
        $metaKey = $this->getPrefixedKey();

        return "LIKE '$metaKey|%'";
    }

    /**
     * @inheritDoc
     */
    public function getValue(array $meta)
    {
        $value = [];

        foreach($meta as $metaResult) {
            $metaBelongsToThisGroup = strpos($metaResult->meta_key, $this->getPrefixedKey() . '|') !== false;

            if ($metaBelongsToThisGroup) {
                /*
                echo $metaResult->meta_key . ' = ' . $metaResult->meta_value;
                echo '<br>';
                */
                
                $metaKeyParts = explode('|', $metaResult->meta_key);
                
                $keys = explode(':', $metaKeyParts[1]);

                $positions =  explode(':', $metaKeyParts[2]);

                if (isset($keys)) {
                    /*
                    echo '<pre>';
                    var_dump($keyArray);
                    echo '</pre>';
                    */

                    $key = $metaKeyParts[1];
                    $index = $metaKeyParts[2];
                    
                    $keys = explode(':', $key);
                    $indices = explode(':', $index);

                    /*
                    echo '<pre>';
                    var_dump($keys);
                    echo '</pre>';'
                    */

                    if (count($indices) === 1) {
                        $field = $this->getField($key);
                        if (!empty($field) && !$field instanceof GroupField) {
                            $value[$index][$key] = $metaResult->meta_value;
                        }
                    } else {
                        $groupIndex = $indices[0];
                        $valueIndex = $indices[1];
                        $groupKey = $keys[0];
                        $valueKey = $keys[1];
                        
                        $value[$groupIndex][$groupKey][$valueIndex][$valueKey] = $metaResult->meta_value;
                    }

                    // TODO: format values using field
                    
                    
                }
            }
        }

        return empty($value) ? $this->defaultValue : $value;
    }

    /**
     * @inheritDoc
     */
    public function createAdmin()
    {
        $this->adminField =  Field::make('complex', $this->key, $this->label)
            ->add_fields($this->getAdminFields());
        
        $this->setSharedOptions();

        foreach ($this->options as $option => $value) {
            switch ($option) {
                case 'duplicate_groups_allowed':
                    $this->adminField->set_duplicate_groups_allowed($value);
                    break;
                case 'layout':
                    $this->adminField->set_layout($value);
                    break;
                case 'collapsed':
                    $this->adminField->set_collapsed($value);
                    break;
                case 'min':
                    $this->adminField->set_min($value);
                    break;
                case 'max':
                    $this->adminField->set_max($value);
                    break;
                case 'labels':
                    $this->adminField->setup_labels($value);
                    break;
            }
        }
    }

    private function setFields(array $fields)
    {
        $this->fields = [];
        foreach ($fields as $field) {
            $this->addField($field);
        }
    }

    private function addField(CFFieldBase $field) : void
    {
        $this->fields[] = $field;
    }

    private function getField($key) : ?CFFieldBase
    {
        foreach ($this->fields as $field) {
            if ($field->key === $key) {
                return $field;
            }
        }

        return null;
    }

    /**
     * Return created admin fields
     *
     * @return array admin fields
     */
    public function getAdminFields() : array {
        $subFields = [];
        foreach ($this->fields as $field) {
            $field->createAdmin();
            $subFields[] = $field->getAdminField();
        }

        return $subFields;
    }
}
