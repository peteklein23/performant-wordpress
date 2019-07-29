<?php

namespace PeteKlein\Performant\Posts;

abstract class PostTypeBase
{
    /** post type slug to be overriden in inherting class */
    const POST_TYPE = null;
    const TYPE_PUBLIC = 'public';
    const TYPE_DEFAULT_API = 'default-api';
    const TYPE_CUSTOM_API = 'custom-api';

    public function __construct()
    {
        if (empty(static::POST_TYPE)) {
            return new \WP_Error(
                'no_post_type_set',
                __('Sorry, you must set a constant of POST_TYPE to inhertit from PostTypeBase', 'performant')
            );
        }
    }
    
    /** function required to register the post type */
    abstract public function register();

    /** function that registers the post type, intended to be called from PostType::registerPostType() */
    protected function registerPostType(
        string $singularLabel,
        string $pluralLabel,
        string $icon,
        string $type,
        int $menuPosition = 25,
        array $supports = ['title', 'editor', 'thumbnail'],
        bool $isHierarchical = false,
        array $argOverrides = []
    ) {
        $pluralSlug = sanitize_title($pluralLabel);

        $labels = $this->getLabels($singularLabel, $pluralLabel);
        $defaultArgs = [
            'labels' => $labels,
            'supports' => $supports,
            'menu_icon' => $icon,
            'hierarchical' => $isHierarchical,
            'rewrite' => false,  // it shouldn't have rewrite rules
            // 'capability_type' => [static::POST_TYPE, $pluralSlug], // register it's own capability for permissions
        ];
        $typeArgs = $this->getArgsForType($type);

        $combinedArgs = array_merge($defaultArgs, $typeArgs);
        $finalArgs = array_merge($combinedArgs, $argOverrides);

        return register_post_type(
            static::POST_TYPE,
            $finalArgs
        );
    }

    private function getLabels(string $singularLabel, string $pluralLabel)
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
    
    private function getArgsForType(string $type)
    {
        if ($type === self::TYPE_PUBLIC) {
            return [
                'public' => true,
                'has_archive' => true,
                'show_in_rest' => true,
            ];
        }
        if ($type === 'default-api') {
            return [
                'public' => false,
                'has_archive' => false,
                'show_in_rest' => false,
            ];
        }
        if ($type === 'custom-api') {
            return [
                'public' => false,
                'has_archive' => false,
                'show_in_rest' => false,
                'publicly_queryable' => true,
                'show_ui' => true,
                'exclude_from_search' => true,
                'show_in_nav_menus' => false,
                'map_meta_cap' => true
            ];
        }
    }
}
