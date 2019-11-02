<?php

namespace PeteKlein\Performant\Fields;

class Field
{
    // un-implemented types
    const TYPE_NUMBER = 'number';
    
    // implemented types
    const TYPE_TEXT = 'text';
    const TYPE_EDITOR = 'editor';
    const TYPE_IMAGE = 'image';
    const TYPE_GROUP = 'group';
    
    /**
     * Factory method to show meta fields in the back-end
     *
     * @param string $key - the meta key
     * @param string $label - the label to be shown in the WordPress admin
     * @param string $type - the type of field to be created
     * @param array $options - additional options to be used in field creation
     * @param mixed $defaultValue - the default value
     * @param boolean $single - whether the field is single or not
     * @return FieldBase
     */
    public static function create(string $key, string $label, string $type, array $options = [], $defaultValue = null, bool $single = true): FieldBase
    {
        
        if ($type === self::TYPE_TEXT) {
            return new TextField($key, $label, $options, $defaultValue, $single);
        }
        if ($type === self::TYPE_EDITOR) {
            return new EditorField($key, $label, $options, $defaultValue, $single);
        }
        if ($type === self::TYPE_GROUP) {
            return new GroupField($key, $label, $options, $defaultValue, $single);
        }
        if ($type === self::TYPE_IMAGE) {
            return new ImageField($key, $label, $options, $defaultValue, $single);
        }
        
        /*
        if ($type === self::TYPE_NUMBER) {
            return new NumberField($key, $label, $type, $options = [], $defaultValue = null, $single = true);
        }
        */
    }
}
