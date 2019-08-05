<?php

namespace PeteKlein\Performant\Taxonomies;

abstract class TaxonomyBase
{
    /**
     * The taxonomy name
     */
    const TAXONOMY = null;

    /**
     * Default arguments for registering a non-hierarchical taxonomy
     */
    const TAG_ARGS = [
        'public' => true,
        'show_admin_column' => true,
        'show_in_rest' => true
    ];
    
    /**
     * Default arguments for registering a hierarchical taxonomy
     */
    const CATEGORY_ARGS = [
        'public' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'hierarchical' => true
    ];

    /**
     * Constructor that checks that the TAXONOMY constant is set on the child
     */
    public function __construct()
    {
        if (empty(static::TAXONOMY)) {
            throw new \Exception(
                _x('You must set the constant TAXONOMY to inhertit from TaxonomyBase', 'no taxonomy constant', 'performant')
            );
        }
    }

    /**
     * Registers the taxonomy, usually by calling TaxonomyBase->registerTaxonomy
     *
     * @return void
     */
    abstract public function register();

    protected function registerTaxonomy(
        array $objectTypes = [],
        array $args = []
    ) {
        register_taxonomy(static::TAXONOMY, $objectTypes, $args);
        
        $this->connectToObjectTypes($objectTypes);
    }

    /**
     * Gets the default labels for registering a data type
     *
     * @param string $singularLabel
     * @param string $pluralLabel
     * @return void
     */
    protected static function getLabels(string $singularLabel, string $pluralLabel)
    {
        return [
            'name' => $pluralLabel,
            'singular_name' => $singularLabel,
            'menu_name' => $pluralLabel,
            'all_items' => _x('All ', 'all taxonomies', 'performant') . $pluralLabel,
            'edit_item' => _x('Edit ', 'edit taxonomy', 'performant') . $singularLabel,
            'view_item' => _x('Veiw ', 'view taxonomy', 'performant') . $singularLabel,
            'update_item' => _x('Update ', 'update taxonomy', 'performant') . $singularLabel,
            'add_new_item' => _x('Add New ', 'add new taxonomy', 'performant') . $singularLabel,
            'new_item_name' => _x('New Name: ', 'new taxonomy name', 'performant') . $singularLabel,
            'parent_item' => _x('Parent ', 'parent taxonomy', 'performant') . $singularLabel,
            'parent_item_colon' => _x('Parent ', 'parent taxonomy', 'performant') . $singularLabel . ':',
            'search_items' => _x('Search ', 'search taxonomies', 'performant') . $pluralLabel,
            'popular_items' => _x('Popular ', 'popular taxonomies', 'performant') . $pluralLabel,
            'separate_items_with_commas' => $pluralLabel . _x('should be separated with commas', 'separate taxonomies with commas', 'performant'),
            'add_or_remove_items' => _x('Add or remove ', 'add or remove taxonomies', 'performant') . $pluralLabel,
            'choose_from_most_used' => _x('Choose from the most used ', 'choose the most used taxonomies', 'performant') . $pluralLabel,
            'not_found' => $pluralLabel . _x(': none found.', 'no taxonomies found', 'performant'),
            'back_to_items' => __('← Back to ', 'performant') . $pluralLabel
            
        ];
    }

    private function connectToObjectTypes(array $objectTypes)
    {
        foreach ($objectTypes as $objectType) {
            register_taxonomy_for_object_type(self::TAXONOMY, $objectType);
        }
    }
}
