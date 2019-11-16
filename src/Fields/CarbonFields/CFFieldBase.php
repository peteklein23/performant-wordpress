<?php

namespace PeteKlein\Performant\Fields\CarbonFields;

use Carbon_Fields\Field;
use Carbon_Fields\Toolset\Key_Toolset;
use PeteKlein\Performant\Fields\FieldBase;

abstract class CFFieldBase extends FieldBase
{
    const DEFAULT_OPTIONS = [
        'required' => false,
        'default' => null,
        'help_text' => '',
        'width' => 100,
        'classes' => '',
        'attributes' => [],
        'show_in_api' => true,
        'conditional_logic' => []
    ];

    protected $adminField;

    /**
     * Executes the code to create a field in the WordPress admin 
     */
    abstract public function createAdminField() : \Carbon_Fields\Field\Field;

    /**
     * @inheritDoc
     */
    abstract public function getSelectionSQL() : string;

    /**
     * @inheritDoc
     */
    abstract public function getValue(array $meta);

    /**
     * @inheritDoc
     */
    abstract public function setAdminOptions() : void;

    public function __construct(string $key, string $label, $defaultValue = null, array $fieldOptionDefaults = [])
    {
        $mergedOptions = array_merge(self::DEFAULT_OPTIONS, $fieldOptionDefaults);
        parent::__construct($key, $label, $defaultValue, $mergedOptions);
    }

    /**
     * Get Carbon Fields prefixed meta key
     *
     * @return void
     */
    public function getPrefixedKey() {
        return Key_Toolset::KEY_PREFIX . $this->key;
    }

    protected function setDefaultAdminOptions() : void
    {
        foreach ($this->options as $option => $value) {
            switch ($option) {
                case 'required':
                    $this->setRequired($value);
                    break;
                case 'help_text':
                    $this->setHelpText($value);
                    break;
                case 'width':
                    $this->setWidth($value);
                    break;
                case 'classes':
                    $this->setClasses($value);
                    break;
                case 'attributes':
                    $this->setAttributes($value);
                    break;
                case 'show_in_api':
                    $this->setShowInAPI($value);
                    break;
                case 'conditional_logic':
                    $this->setConditionalLogic($value);
                    break;
            }
        }

        $this->setDefault($this->defaultValue);
    }

    private function setRequired(bool $required) : void
    {
        $this->adminField->set_required($required);
    }

    private function setDefault($default) : void
    {
        $this->adminField->set_default_value($default);
    }

    private function setHelpText(string $helpText) : void
    {
        $this->adminField->set_help_text($helpText);
    }

    private function setWidth(int $width) : void
    {
        $this->adminField->set_width($width);
    }

    private function setClasses(string $classes) : void
    {
        $this->adminField->set_classes($classes);
    }

    private function setAttributes(array $attributes) : void
    {
        foreach ($attributes as $key => $value) {
            $this->adminField->set_attribute($key, $value);
        }
    }

    private function setShowInAPI (bool $showInAPI) : void
    {
        $this->adminField->set_visible_in_rest_api($showInAPI);
    }

    private function setConditionalLogic (array $conditionalLogic) : void
    {
        $this->adminField->set_conditional_logic($conditionalLogic);
    }
}
