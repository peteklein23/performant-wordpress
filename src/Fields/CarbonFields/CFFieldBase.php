<?php

namespace PeteKlein\Performant\Fields\CarbonFields;

use Carbon_Fields\Field\Field;
use Carbon_Fields\Toolset\Key_Toolset;
use PeteKlein\Performant\Fields\FieldBase;

abstract class CFFieldBase extends FieldBase
{
    protected $adminField;

    /**
     * @inheritDoc
     */
    abstract public function createAdmin();

    /**
     * @inheritDoc
     */
    abstract public function getSelectionSQL() : string;

    /**
     * @inheritDoc
     */
    abstract public function getValue(array $meta);

    public function __construct(string $key, string $label, $defaultValue = null, array $options = [])
    {
        parent::__construct($key, $label, $defaultValue, $options);
    }

    /**
     * Get Carbon Fields prefixed meta key
     *
     * @return void
     */
    public function getPrefixedKey() {
        return Key_Toolset::KEY_PREFIX . $this->key;
    }

    /**
     * Combine the default and override options
     *
     * @param array $fieldDefaults
     * @param array $options
     * @return array
     */
    protected function combineOptions(array $fieldDefaults, array $options): array
    {
        $sharedDefaults = [
            'required' => false,
            'default' => null,
            'help_text' => '',
            'width' => 100,
            'classes' => '',
            'attributes' => [],
            'show_in_api' => true,
            'conditional_logic' => []
        ];
        $defaults = array_merge($sharedDefaults, $fieldDefaults);
        
        return array_merge($defaults, $options);
    }

    /**
     * Set the shared options for this field
     *
     * @return void
     */
    protected function setSharedOptions() : void
    {
        foreach ($this->options as $option => $value) {
            switch ($option) {
                case 'required':
                    $this->adminField->set_required($value);
                    break;
                case 'help_text':
                    $this->adminField->set_help_text($value);
                    break;
                case 'width':
                    $this->adminField->set_width($value);
                    break;
                case 'classes':
                    $this->adminField->set_classes($value);
                    break;
                case 'show_in_api':
                    $this->adminField->set_visible_in_rest_api($value);
                    break;
                case 'default_value':
                    $this->adminField->set_default_value($value);
                    break;
                case 'conditional_logic':
                    $this->adminField->set_conditional_logic($value);
                    break;
                case 'attributes':
                    $this->setAttributes($value);
                    break;
            }
        }
    }

    /**
     * Set attributes on this field
     *
     * @param array $attributes
     * @return void
     */
    private function setAttributes(array $attributes) : void
    {
        foreach ($attributes as $key => $value) {
            $this->adminField->set_attribute($key, $value);
        }
    }

    public function getAdminField(): Field
    {
        return $this->adminField;
    }
}
