<?php

namespace PeteKlein\Performant\Posts;

use PeteKlein\Performant\Posts\Meta\PostMetaCollection;
use PeteKlein\Performant\Posts\Meta\PostMetaBox;
use PeteKlein\Performant\Posts\Taxonomies\PostTaxonomyCollection;
use PeteKlein\Performant\Posts\FeaturedImages\FeaturedImageCollection;

abstract class PostTypeBase
{
    /**
     * post type slug to be overriden in inherting class
     */
    const POST_TYPE = null;
    
    const PUBLIC_ARGS = [
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'menu_position' => 25,
        'supports' => ['title', 'editor', 'thumbnail', 'author']
    ];

    const PRIVATE_ARGS = [
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
    ];

    protected $meta;
    protected $metaBoxes = [];
    // protected $taxonomies;
    // protected $featuredImages;

    public function __construct()
    {
        if (empty(static::POST_TYPE)) {
            throw new \Exception(__('You must set the constant POST_TYPE to inhertit from PostTypeBase', 'performant'));
        }
    }

    public function create()
    {
        $this->meta = new PostMetaCollection();
        // $this->taxonomies = new PostTaxonomyCollection();
        // $this->featuredImages = new FeaturedImageCollection();

        $this->registerPostType();
        $this->registerMeta();
        $this->registerMetaBoxes();
    }
    
    /**
     * Registers the post type by calling PostType->registerPostType()
     */
    abstract public function registerPostType();

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
            'view_item' => __('View', 'performant') . ' ' .$singularLabel,
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

    protected function register(array $args = [])
    {
        $registeredPostType = register_post_type(
            static::POST_TYPE,
            $args
        );

        if (is_wp_error($registeredPostType)) {
            throw new \Exception('There was an issue registering the post type.');
        }

        return $registeredPostType;
    }
    
    /**
     * Register your meta fields here
     */
    protected function registerMeta()
    {
    }

    /**
     * Add meta to metaboxes to edit them in the admin here
     */
    protected function registerMetaBoxes()
    {
    }

    protected function addMeta(string $key, string $label, string $type, array $typeOptions = [], $defaultValue = null, bool $single = true)
    {
        $this->meta->addField($key, $label, $type, $typeOptions, $defaultValue, $single);

        return $this;
    }

    protected function addMetaBox(string $label, array $metaKeys)
    {
        $metaBox = new PostMetaBox(static::POST_TYPE, $label);
        foreach ($metaKeys as $key) {
            $field = $this->meta->getField($key);
            if (!empty($field)) {
                $metaBox->addField($field->getAdminField());
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
}
