<?php

namespace PeteKlein\Performant;

use PeteKlein\Performant\Posts\PostTypeBase;

class PerformantTheme
{
    public function __construct()
    {
        $this->registerDefaultHooks();
    }

    public function registerDefaultHooks()
    {
        add_action('after_setup_theme', [$this, 'initTheme']);
        add_action('wp_enqueue_scripts', [$this, 'registerScripts']);
        add_action('wp_enqueue_scripts', [$this, 'registerStyles']);
        add_action('widgets_init', [$this, 'registerSidebars']);
        add_action('widgets_init', [$this, 'registerWidgets']);
        add_action('admin_menu', [$this, 'registerAdminScreens']);
        add_action('admin_enqueue_scripts', [$this, 'registerAdminScripts']);
        add_action('admin_enqueue_scripts', [$this, 'registerAdminStyles']);
    }

    public function initTheme()
    {
        $this->addThemeSupport();
        $this->registerImageSizes();
        $this->registerTaxonomies();
        $this->registerPostTypes();
        $this->registerShortcodes();
        $this->registerNavMenus();
    }
    
    public function addThemeSupport()
    {
        add_theme_support('title-tag');
        add_theme_support('automatic-feed-links');
        add_theme_support('post-thumbnails');
        add_theme_support('html5', ['search-form']);
    }

    /**
     * Register your post types here
     */
    public function registerPostTypes()
    {
    }

    /**
     * Get Post Type 
     * 
     * @return void
     */
    public function getPostType(string $slug) : ?PostTypeBase
    {
        foreach ($this->postTypes as $postType) {
            if ($postType::POST_TYPE === $slug) {
                return $postType;
            }
        }

        return null;
    }

    /**
     * Register your taxonomies with `$this->setTaxonomies`
     */
    protected function registerTaxonomies()
    {
    }

    /**
     * Register image sizes here
     *
     * @return void
     */
    protected function registerImageSizes() : void 
    {}

    /**
     * Register your shortcodes here
     */
    public function registerShortcodes()
    {
    }

    /**
     * Register your nav menus here
     */
    public function registerNavMenus()
    {
    }

    /**
     * register your scripts here
     *
     * @return void
     */
    public function registerScripts()
    {
    }

    public function registerStyles()
    {
    }

    public function registerSidebars()
    {
    }
    
    public function registerAdminScreens()
    {
    }

    public function registerWidgets()
    {
    }

    public function registerAdminScripts()
    {
    }

    public function registerAdminStyles()
    {
    }
}
