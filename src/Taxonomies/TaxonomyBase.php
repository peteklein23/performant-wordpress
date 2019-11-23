<?php

namespace PeteKlein\Performant\Taxonomies;

use PeteKlein\Performant\Patterns\Singleton;

abstract class TaxonomyBase extends Singleton
{
    /**
     * The taxonomy name
     */
    const TAXONOMY = '';
    const TAG_TYPE = 'tag';
    const CATEGORY_TYPE = 'category';

    abstract public function register() : void;

    protected function __construct()
    {
        if (empty(static::TAXONOMY)) {
            throw new \Exception(
                _x('You must set the constant TAXONOMY to inherit from TaxonomyBase', 'no taxonomy constant', 'performant')
            );
        }
    }

    /**
     * Return default registration args
     *
     * @param string $argType - self::TAG_TYPE or self::CATEGORY_TYPE
     * @param string $singularLabel
     * @param string $pluralLabel
     * 
     * @return void
     */
    public static function getRegistrationArgs(
        string $argType, 
        string $singularLabel, 
        string $pluralLabel
    ) : array {
        $defaultArgs = [
            'labels' => self::getLabels($singularLabel, $pluralLabel),
            'public' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
        ];

        if ($argType === self::TAG_TYPE) {
            return array_merge($defaultArgs, [
                'hierarchical' => false
            ]);
        }

        if ($argType === self::CATEGORY_TYPE) {
            return array_merge($defaultArgs, [
                'hierarchical' => true
            ]);
        }

        return $defaultArgs;
    }

    /**
     * Gets the default labels for registering a data type
     *
     * @param string $singularLabel
     * @param string $pluralLabel
     * @return void
     */
    protected static function getLabels(string $singularLabel, string $pluralLabel) : array
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

    /**
     * Register taxonomies and connect object types
     *
     * @param array $postTypes - post types, nav_menu_items, etc
     * @param array $args - taxonomy registration args
     * @see https://codex.wordpress.org/Function_Reference/register_taxonomy
     * @return void
     */
    protected function registerTaxonomy (
        array $postTypes = [],
        array $args = []
    ) : void {
        register_taxonomy(static::TAXONOMY, $postTypes, $args);
    }
}
