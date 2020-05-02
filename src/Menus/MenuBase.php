<?php

namespace PeteKlein\Performant\Menus;

use PeteKlein\Performant\Patterns\Singleton;

abstract class MenuBase extends Singleton
{
    /**
     * menu location to be overridden in inheriting class
     */
    const LOCATION = '';

    protected static $instances = [];
    private $themeSlug;

    /**
     * Registers the post type by calling Menu->register()
     */
    abstract public function register();

    public function __construct($themeSlug)
    {
        if (empty(static::LOCATION)) {
            throw new \Exception(__('You must set the constant LOCATION in your inheriting class', 'performant'));
        }

        $this->themeSlug = $themeSlug;
    }

    /**
     * @inheritDoc
     *
     * @return MenuBase
     */
    public static function getInstance(): MenuBase
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static;
        }

        return self::$instances[$cls];
    }

    /**
     * Register the menu
     *
     * @see https://codex.wordpress.org/Function_Reference/register_post_type#Arguments
     * @param array $args
     * @return void
     */
    protected function registerMenu($description)
    {
        $registeredMenu = register_nav_menu(static::LOCATION, $description);

        if (is_wp_error($registeredMenu)) {
            throw new \Exception('There was an issue registering the menu.');
        }
    }

    /**
     * Get the menu id from it's registered location
     *
     * @return void
     */
    public function getMenuId() : int
    {
        global $wpdb;

        $query = "SELECT
            option_value
        FROM $wpdb->options 
        WHERE option_name = 'theme_mods_$this->themeSlug'";
        
        $themeOptionsResult = $wpdb->get_var($query);
        if ($themeOptionsResult === false) {
            throw new \Exception('This there are no theme options registered for theme with "' . $this->themeSlug . '". Please make sure the theme is activated and the theme slug matches your directory.');
        }
        $themeOptions = maybe_unserialize($themeOptionsResult);

        $navMenuLocations = $themeOptions['nav_menu_locations'];
        if (empty($navMenuLocations[static::LOCATION])) {
            throw new \Exception('This theme has no menu options');
        }

        if (empty($navMenuLocations[static::LOCATION])) {
            throw new \Exception('There is no menu registered at the location ' . '"' . static::LOCATION . '." Please add a theme in the WordPress admin at that location to retrieve the ID.');
        }

        return (int) $navMenuLocations[static::LOCATION];
    }

    private function formatResults(array $results) {
        $formattedResults = [];
        if(empty($results)){
            return $formattedResults;
        }
        
        foreach($results as $result) {
            $formattedResults[] = [
                'post_title' => $result->post_title,
                'url' => !empty($result->url) ? $result->url : get_permalink($results->object_id)
            ];
        }

        return $formattedResults;
    }

    public function getMenuItems() {
        global $wpdb;

        $menuId = $this->getMenuId();

        $query = "SELECT
            p.ID, 
            p.post_title,
            objectIdMeta.meta_value AS object_id,
            urlMeta.meta_value AS url,
            parentMeta.meta_value AS parent
        FROM $wpdb->term_relationships tr
        INNER JOIN $wpdb->posts p ON tr.object_id = p.ID AND p.post_status = 'publish'
        INNER JOIN $wpdb->term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
        INNER JOIN $wpdb->terms t ON t.term_id = tt.term_id AND tt.term_id = $menuId
        LEFT JOIN $wpdb->postmeta objectIdMeta ON p.ID = objectIdMeta.post_id AND objectIdMeta.meta_key = '_menu_item_object_id' 
        LEFT JOIN $wpdb->postmeta urlMeta ON urlMeta.meta_key = '_menu_item_url' AND urlMeta.post_id = p.ID
        LEFT JOIN $wpdb->postmeta parentMeta ON parentMeta.meta_key = '_menu_item_menu_item_parent' AND parentMeta.post_id = p.ID
        ORDER BY p.menu_order;
        ";

        $menuItems = $wpdb->get_results($query);

        return $this->formatResults($menuItems);
    }
}
