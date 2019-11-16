<?php

namespace PeteKlein\Performant\Fields\CarbonFields;

use Carbon_Fields\Field;

class GroupField extends CFFieldBase
{
    private $fields = [];

    /**
     * @inheritDoc
     */
    public function __construct(string $key, string $label, array $fields, array $options = [], $defaultValue = null, bool $single = true)
    {
        parent::__construct($key, $label, $options, $defaultValue, $single);
        $this->setFields($fields);
    }

    /**
     * @inheritDoc
     */
    public function createAdminField() : \Carbon_Fields\Field\Field
    {
        return Field::make('complex', $this->key, $this->label)
            // ->set_layout('tabbed-horizontal')
            ->add_fields($this->getAdminFields());
    }

    /**
     * @inheritDoc
     */
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
            if (strpos($metaResult->meta_key, $this->getPrefixedKey()) !== false) {

                /*
                echo $metaResult->meta_key . ' = ' . $metaResult->meta_value;
                echo '<br>';
                */
                
                $keyArray = explode('|', $metaResult->meta_key);
                if (isset($keyArray[1]) && $keyArray[1] !== '') {
                    /*
                    echo '<pre>';
                    var_dump($keyArray);
                    echo '</pre>';
                    */

                    $key = $keyArray[1];
                    $index = $keyArray[2];
                    
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
        foreach($this->fields as $field){
            if($field->key === $key) {
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
        foreach($this->fields as $field){
            $subFields[] = $field->createAdminField();
        }

        return $subFields;
    }
}
