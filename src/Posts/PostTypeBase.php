<?php

namespace PeteKlein\Performant\Posts;

use PeteKlein\Performant\Fields\FieldBase;
use PeteKlein\Performant\Fields\FieldGroupBase;
use PeteKlein\Performant\Images\ImageSizeBase;
use PeteKlein\Performant\Patterns\Singleton;
use PeteKlein\Performant\Posts\FeaturedImages\FeaturedImageCollection;
use PeteKlein\Performant\Posts\Meta\PostMetaBox;
use PeteKlein\Performant\Posts\Meta\PostMetaCollection;
use PeteKlein\Performant\Posts\Taxonomies\PostTaxonomyCollection;
use PeteKlein\Performant\Taxonomies\TaxonomyBase;

abstract class PostTypeBase extends Singleton
{
    /**
     * post type slug to be overridden in inheriting class
     */
    const POST_TYPE = '';
    const PUBLIC_TYPE = 'public';
    const PRIVATE_TYPE = 'private';

    protected $meta;
    protected $taxonomies;

    /**
     * Registers the post type by calling PostType->register()
     */
    abstract public function register();

    public function __construct()
    {
        if (empty(static::POST_TYPE)) {
            throw new \Exception(__('You must set the constant POST_TYPE in your inheriting class', 'performant'));
        }

        $this->meta = new PostMetaCollection();
        $this->taxonomies = new PostTaxonomyCollection();
        $this->featuredImages = new FeaturedImageCollection();

        $this->registerPostType();
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
    public static function getRegistrationArgs(
        string $argType, 
        string $singularLabel, 
        string $pluralLabel, 
        string $icon = 'dashicons-admin-post'
    ) {
        $defaultArgs = [
            'labels' => self::getLabels($singularLabel, $pluralLabel),
            'menu_icon' => $icon
        ];
        if ($argType === self::PUBLIC_TYPE) {
            return array_merge($defaultArgs, [
                'public' => true,
                'has_archive' => true,
                'show_in_rest' => true,
                'menu_position' => 25,
                'supports' => ['title', 'editor', 'thumbnail', 'author']
            ]);
        }

        if ($argType === self::PRIVATE_TYPE) {
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

    /**
     * Register the post type with the 
     *
     * @see https://codex.wordpress.org/Function_Reference/register_post_type#Arguments
     * @param array $args
     * @return void
     */
    protected function registerPostType(array $args = [])
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
     * Set field groups
     *
     * @param array $fieldGroups
     * @return void
     */
    protected function setFieldGroups(array $fieldGroups) : void
    {
        if (empty($fieldGroups)) {
            return;
        }
        foreach ($fieldGroups as $fieldGroup) {
            $this->addFieldGroup($fieldGroup);
        }
    }

    /**
     * Create a meta box and add fields 
     *
     * @param string $label
     * @param array $metaKeys
     * @return void
     */
    protected function addFieldGroup(FieldGroupBase $fieldGroup) : void
    {
        $name = $fieldGroup->getName();
        $metaBox = new PostMetaBox(static::POST_TYPE, $name);
        foreach ($fieldGroup->listFields() as $field) {
            $this->meta->addField($field);
            $metaBox->addAdminField($field->createAdminField());
        }
    }

    /**
     * Adds a field to the meta collection
     *
     * @param FieldBase $field
     */
    protected function addMeta(FieldBase $field) : PostTypeBase
    {
        $this->meta->addField($field);

        return $this;
    }

    /**
     * List meta fields for multiple posts
     *
     * @param array $postIds
     * @return void
     */
    public function listMeta(array $postIds = [])
    {
        $this->meta->fetch($postIds);

        return $this->meta->list();
    }

    /**
     * Add Taxonomies
     *
     * @param array $taxonomies - array of taxonomies
     * @return void
     */
    protected function setTaxonomies(array $taxonomies) : void
    {
        if (empty($taxonomies)) {
            return;
        }
        foreach ($taxonomies as $taxonomy) {
            $this->addTaxonomy($taxonomy);
        }
    }

    /**
     * Adds a taxonomy to the taxonomy collection
     *
     * @param TaxonomyBase $taxonomy
     */
    private function addTaxonomy(TaxonomyBase $taxonomy = null) : PostTypeBase
    {
        $this->taxonomies->addTaxonomy($taxonomy);

        return $this;
    }

    /**
     * List taxonomy terms for multiple posts
     *
     * @param array $postIds
     * @return void
     */
    public function listTaxonomies(array $postIds = [])
    {
        $this->taxonomies->fetch($postIds);

        return $this->taxonomies->list();
    }

    /**
     * Register your featured image sizes here
     */
    protected function setFeaturedImageSizes(array $imageSizes) : void
    {
        foreach ($imageSizes as $imageSize) {
            $this->addFeaturedImageSize($imageSize);
        }
    }

    /**
     * Add image size to the featured image collection
     *
     * @param ImageSizeBase $imageSize
     * @return PostTypeBase
     */
    protected function addFeaturedImageSize(ImageSizeBase $imageSize) : PostTypeBase
    {
        $this->featuredImages->addSize($imageSize::SIZE);

        return $this;
    }

    public function listFeatureImages(array $postIds = [])
    {
        $this->featuredImages->fetch($postIds);

        return $this->featuredImages->list();
    }
}
