<?php

namespace PeteKlein\Performant\Posts;

use PeteKlein\Performant\Fields\FieldBase;
use PeteKlein\Performant\Posts\Meta\PostMetaCollection;
use PeteKlein\Performant\Posts\Meta\PostMetaBox;
use PeteKlein\Performant\Posts\Taxonomies\PostTaxonomyCollection;
use PeteKlein\Performant\Posts\FeaturedImages\FeaturedImageCollection;

abstract class PostTypeBase
{
    /**
     * post type slug to be overridden in inheriting class
     */
    const POST_TYPE = '';
    const PUBLIC_ARG_TYPE = 'public';
    const PRIVATE_ARG_TYPE = 'private';

    protected $meta;
    protected $metaBoxes = [];
    // protected $taxonomies;
    // protected $featuredImages;

    /**
     * Registers the post type by calling PostType->register()
     */
    abstract public function registerPostType();

    public function __construct()
    {
        if (empty(static::POST_TYPE)) {
            throw new \Exception(__('You must set the constant POST_TYPE to inherit from PostTypeBase', 'performant'));
        }

        $this->meta = new PostMetaCollection();
        // $this->taxonomies = new PostTaxonomyCollection();
        // $this->featuredImages = new FeaturedImageCollection();
    }

    /**
     * Get the default registration args, assuming common default settings
     *
     * @see https://codex.wordpress.org/Function_Reference/register_post_type#Arguments
     * @param string $argType 'public' or 'private'
     * @param string $singularLabel singular label to be used in the admin
     * @param string $pluralLabel plural label to be used in the admin
     * @param string $icon the icon, e.g. 'dashicons-admin-post'
     * @return array registration args
     */
    public static function getRegistrationArgs(string $argType, string $singularLabel, string $pluralLabel, string $icon)
    {
        $defaultArgs = [
            'labels' => self::getLabels($singularLabel, $pluralLabel),
            'menu_icon' => $icon
        ];
        if ($argType === self::PUBLIC_ARG_TYPE) {
            return array_merge($defaultArgs, [
                'public' => true,
                'has_archive' => true,
                'show_in_rest' => true,
                'menu_position' => 25,
                'supports' => ['title', 'editor', 'thumbnail', 'author']
            ]);
        }

        if ($argType === self::PRIVATE_ARG_TYPE) {
            return array_merge($defaultArgs, [
                'public' => false,
                'has_archive' => false,
                'show_in_rest' => false,
                'publicly_queryable' => true,
                'show_ui' => true,
                'exclude_from_search' => true,
                'show_in_nav_menus' => false,
                'map_meta_cap' => true,
                'menu_position' => 25,
                'supports' => ['title', 'editor', 'thumbnail', 'author']
            ]);
        }

        return $defaultArgs;
    }

    public function create()
    {
        $this->registerPostType();
        $this->registerMeta();
        $this->registerMetaBoxes();

        return $this;
    }

    /**
     * Gets the default label set for registering a post type
     *
     * @param string $singularLabel
     * @param string $pluralLabel
     * @return array $labels @see https://codex.wordpress.org/Function_Reference/register_post_type#Arguments
     */
    protected static function getLabels(string $singularLabel, string $pluralLabel)
    {
        return [
            'name' => $pluralLabel,
            'singular_name' => $singularLabel,
            'add_new_item' => __('Add New', 'performant') . ' ' . $singularLabel,
            'edit_item' => __('Edit', 'performant') . ' ' . $singularLabel,
            'new_item' => __('New', 'performant') . ' ' . $singularLabel,
            'view_item' => __('View', 'performant') . ' ' . $singularLabel,
            'search_items' => __('Search', 'performant') . ' ' . $pluralLabel,
            'not_found' => __('None found', 'performant'),
            'not_found_in_trash' => __('None found in trash', 'performant'),
            'parent_item_colon' => __('Parent ', 'performant') . ' ' . $pluralLabel,
            'all_items' => __('All ', 'performant') . ' ' . $pluralLabel,
            'archives' => $pluralLabel . ' ' . __('Archives', 'performant'),
            'attributes' => $pluralLabel . ' ' . __('Attributes', 'performant'),
            'insert_into_item' => __('Insert into', 'performant') . ' ' . $singularLabel,
            'uploaded_to_this_item' => __('Uploaded to ', 'performant') . ' ' . $singularLabel,
            'item_published' => $singularLabel . ' ' . __('published', 'performant'),
            'item_published_privately' => $singularLabel . ' ' . __('published privately', 'performant'),
            'item_reverted_to_draft' => $singularLabel . ' ' . __('reverted to draft', 'performant'),
            'item_scheduled' => $singularLabel . ' ' . __('scheduled', 'performant'),
            'item_updated' => $singularLabel . ' ' . __('updated', 'performant'),
        ];
    }

    /**
     * Register the post type with the 
     *
     * @see https://codex.wordpress.org/Function_Reference/register_post_type#Arguments
     * @param array $args
     * @return void
     */
    protected function register(array $args = [])
    {
        $registeredPostType = register_post_type(
            static::POST_TYPE,
            $args
        );

        if (is_wp_error($registeredPostType)) {
            throw new \Exception('There was an issue registering the post type.');
        }
    }

    /**
     * Register your meta fields here
     */
    protected function registerMeta()
    { }

    /**
     * Add meta to meta boxes to edit them in the admin here
     */
    protected function registerMetaBoxes()
    { }

    protected function addMeta(FieldBase $field)
    {
        $this->meta->addField($field);

        return $this;
    }

    protected function addMetaBox(string $label, array $metaKeys)
    {
        $metaBox = new PostMetaBox(static::POST_TYPE, $label);
        foreach ($metaKeys as $key) {
            $field = $this->meta->getField($key);
            if (!empty($field)) {
                $metaBox->addField($field->createAdminField());
            }
        }
        $this->metaBoxes[] = $metaBox;
    }

    /*
    protected function addTaxonomy(string $taxonomy, $default)
    {
        $this->taxonomies->addField($taxonomy, $default);

        return $this;
    }

    protected function addImageSize(string $size)
    {
        $this->featuredImages->addSize($size);

        return $this;
    }
    */

    public function listMeta($postIds = [])
    {
        $this->meta->fetch($postIds);

        return $this->meta->list();
    }
}
