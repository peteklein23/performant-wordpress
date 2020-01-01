<?php

namespace PeteKlein\Performant\Fields\CarbonFields;

use Carbon_Fields\Field;

class PostTypeField extends CFFieldBase
{
    private $postTypeSlug;

    public function __construct(
        string $key, 
        string $label, 
        $defaultValue = [],
        string $postTypeSlug,
        array $options = []
    ) {
        $this->postTypeSlug = $postTypeSlug;
        $fieldDefaults = [
            'min' => -1,
            'max' => -1,
            'duplicates_allowed' => false
        ];
        $combinedOptions = $this->combineOptions($fieldDefaults, $options);

        parent::__construct($key, $label, $defaultValue, $combinedOptions);
    }

    /**
     * @inheritDoc
     */
    public function createAdmin() : void
    {
        $this->adminField = Field::make('association', $this->key, $this->label)
            ->set_types([
                [
                    'type' => 'post',
                    'post_type' => $this->postTypeSlug
                ]
            ]);
        $this->setSharedOptions();

        foreach ($this->options as $option => $value) {
            switch ($option) {
                case 'min':
                    $this->adminField->set_min($value);
                    break;
                case 'max':
                    $this->adminField->set_max($value);
                    break;
                case 'duplicates_allowed':
                    $this->adminField->set_duplicates_allowed($value);
                    break;
            }
        }
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
    public function getValue(array $results)
    {
        $value = [];
        foreach ($results as $meta) {
            $metaBelongsToThisField = strpos($meta->meta_key, $this->getPrefixedKey() . '|') !== false;

            if(!$metaBelongsToThisField) {
                continue;
            }
            
            $metaKeyParts = explode('|', $meta->meta_key);
            $identifier = $metaKeyParts[4];
            if($identifier !== 'id') {
                continue;
            }

            $index = $metaKeyParts[3];
            $value[$index] = (int) $meta->meta_value;
        }

        return empty($value) ? $this->defaultValue : $value;
    }
}
