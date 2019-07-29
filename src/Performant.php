<?php

namespace PeteKlein\Performant;

use PeteKlein\Performant\Registration\Registrar;

class Performant
{
    public function __construct()
    {
        $this->addHooks();
    }

    protected function addHooks()
    {
        add_action('after_setup_theme', [$this, 'init']);
        add_action('wp_enqueue_scripts', [$this, 'loadScripts']);
        add_action('wp_enqueue_scripts', [$this, 'loadStyles']);
    }

    public function init()
    {
        $this->addThemeSupport();
        $this->initCarbonFields();
        $this->registerPostTypes();
        $this->registerTaxonomies();
        $this->addShortcodes();
    }
    
    protected function addThemeSupport()
    {
        add_theme_support('title-tag');
        add_theme_support('automatic-feed-links');
        add_theme_support('post-thumbnails');
        add_theme_support('html5', ['search-form']);
    }

    protected function initCarbonFields()
    {
        \Carbon_Fields\Carbon_Fields::boot();
    }

    protected function registerPostTypes()
    {
    }

    protected function registerTaxonomies()
    {
    }

    public function loadScripts()
    {
    }

    public function loadStyles()
    {
    }

    protected function addShortcodes()
    {
    }
}
