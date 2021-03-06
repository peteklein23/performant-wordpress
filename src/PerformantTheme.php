<?php

namespace PeteKlein\Performant;

use PeteKlein\Performant\Patterns\Singleton;

abstract class PerformantTheme extends Singleton
{
    /**
     * this should match the theme folder name
     */
    const THEME_SLUG = '';

    protected static $instances = [];
    protected $themeMods = null;

    abstract protected function register();

    public function __construct()
    {
        if (empty(static::THEME_SLUG)) {
            throw new \Exception(
                __(
                    'You must set the constant THEME_SLUG in your inheriting class',
                    'performant'
                )
            );
        }
    }

    /**
     * @inheritDoc
     *
     * @return PerformantTheme
     */
    public static function getInstance(): PerformantTheme
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }

        return self::$instances[$cls];
    }

    public function registerTheme(): void
    {
        $this->registerDefaultHooks();
        $this->themeMods = self::getThemeMods();
    }

    public static function getThemeMods()
    {
        global $wpdb;

        $query =
            "SELECT
            option_value
        FROM $wpdb->options 
        WHERE option_name = 'theme_mods_" .
            static::THEME_SLUG .
            "'";

        $themeModsResult = $wpdb->get_var($query);
        if ($themeModsResult === false) {
            trigger_error(
                'This there are no theme mods registered for theme with "' .
                    static::THEME_SLUG .
                    '". Please make sure the theme is activated and the theme slug matches your theme directory.'
            );

            return null;
        }

        return maybe_unserialize($themeModsResult);
    }

    public function registerDefaultHooks()
    {
        add_action('after_setup_theme', [$this, 'initTheme']);
        add_action('wp_enqueue_scripts', [$this, 'registerScripts']);
        add_action('wp_enqueue_scripts', [$this, 'registerStyles']);
        add_action('wp_enqueue_scripts', [$this, 'registerComponents']);
        add_action('widgets_init', [$this, 'registerSidebars']);
        add_action('widgets_init', [$this, 'registerWidgets']);
        add_action('admin_menu', [$this, 'registerAdminScreens']);
        add_action('admin_enqueue_scripts', [$this, 'registerAdminScripts']);
        add_action('admin_enqueue_scripts', [$this, 'registerAdminStyles']);
    }

    public function initTheme()
    {
        $this->addThemeSupport();
        $this->registerUserRoles();
        $this->registerImageSizes();
        $this->registerTaxonomies();
        $this->registerPostTypes();
        $this->registerShortcodes();
        $this->registerMenus();
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
     * Register your components here
     */
    public function registerComponents()
    {
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
    protected function registerImageSizes(): void
    {
    }

    /**
     * Register user roles here
     *
     * @return void
     */
    public function registerUserRoles(): void
    {
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
    public function registerMenus()
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

    public function getTemplatePart(string $filePath)
    {
        $fullPath =
            WP_CONTENT_DIR . '/themes/' . static::THEME_SLUG . '/' . $filePath;
        include $fullPath;
    }
}
