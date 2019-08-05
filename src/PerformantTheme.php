<?php

namespace PeteKlein\Performant;

class PerformantTheme
{
    public function __construct()
    {
        $this->registerHooks();
    }

    public function registerHooks()
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
        $this->initCarbonFields();
        $this->registerPostTypes();
        $this->registerTaxonomies();
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

    public function initCarbonFields()
    {
        \Carbon_Fields\Carbon_Fields::boot();
    }

    public function registerPostTypes()
    {
    }

    public function registerTaxonomies()
    {
    }

    public function registerShortcodes()
    {
    }

    public function registerNavMenus()
    {
    }

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
