<?php

namespace PeteKlein\Performant;

use PeteKlein\Performant\Posts\PostTypeBase;
use PeteKlein\Performant\Taxonomies\TaxonomyBase;
use PeteKlein\Performant\Images\ImageSizeBase;

class PerformantTheme
{
    private $postTypes = [];
    private $taxonomies = [];
    private $imageSizes = [];

    public function __construct()
    {
        $this->registerHooks();
    }

    public function registerHooks()
    {
        add_action('after_setup_theme', [$this, 'initTheme']);
        add_action('init', [$this, 'afterInitTheme']);
        add_action('wp_enqueue_scripts', [$this, 'registerScripts']);
        add_action('wp_enqueue_scripts', [$this, 'registerStyles']);
        add_action('widgets_init', [$this, 'registerSidebars']);
        add_action('widgets_init', [$this, 'registerWidgets']);
        add_action('admin_menu', [$this, 'registerAdminScreens']);
        add_action('admin_enqueue_scripts', [$this, 'registerAdminScripts']);
        add_action('admin_enqueue_scripts', [$this, 'registerAdminStyles']);
    }

    public function beforeInitTheme()
    {
    }

    public function initTheme()
    {
        $this->beforeInitTheme();
        $this->addThemeSupport();
        $this->initCarbonFields();
        $this->registerImageSizes();
        $this->registerTaxonomies();
        $this->registerPostTypes();
        $this->registerShortcodes();
        $this->registerNavMenus();
        $this->afterInitTheme();
    }

    public function afterInitTheme()
    {
    }
    
    public function addThemeSupport()
    {
        add_theme_support('title-tag');
        add_theme_support('automatic-feed-links');
        add_theme_support('post-thumbnails');
        add_theme_support('html5', ['search-form']);
    }

    // TODO: move carbon fields into it's own concept and out of theme
    public function initCarbonFields()
    {
        \Carbon_Fields\Carbon_Fields::boot();
    }

    /**
     * Register your post types here
     */
    public function registerPostTypes()
    {
    }

    /**
     * Add a post type
     * 
     * @return void
     */
    public function addPostType(PostTypeBase $postType) : void
    {
        $this->postTypes[] = $postType;
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
     * Register your taxonomies here
     */
    public function registerTaxonomies()
    {
    }

    /**
     * Add a taxonomy
     * 
     * @return void
     */
    public function addTaxonomy(TaxonomyBase $taxonomy) : void
    {
        $this->taxonomies[] = $taxonomy;
    }

    /**
     * return a registered taxonomy
     *
     * @param string $slug
     */
    public function getTaxonomy(string $slug) : ?TaxonomyBase
    {
        foreach($this->taxonomies as $taxonomy) {
            if ($taxonomy::TAXONOMY === $slug) {
                return $taxonomy;
            }
        }

        return null;
    }

    /**
     * Register image sizes here
     *
     * @return void
     */
    public function registerImageSizes() : void 
    {}

    /**
     * Add image size to the list of image sizes
     *
     * @param ImageSizeBase $imageSize
     * @return void
     */
    public function addImageSize(ImageSizeBase $imageSize) : void
    {
        $this->imageSizes[] = $imageSize;
    }

    /**
     * return a registered image size
     *
     * @param string $slug
     * @return ImageSizeBase|null
     */
    public function getImageSize(string $slug) : ?ImageSizeBase
    {
        foreach($this->imageSizeBase as $imageSize) {
            if ($imageSize::NAME === $slug) {
                return $imageSize;
            }
        }

        return null;
    }

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
