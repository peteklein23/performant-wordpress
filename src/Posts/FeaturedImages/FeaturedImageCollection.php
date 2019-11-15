<?php

namespace PeteKlein\Performant\Posts\FeaturedImages;

use PeteKlein\Performant\Images\ImageSizeBase;

class FeaturedImageCollection
{
    private $images = [];
    private $sizes = [];

    /**
     * Add a featured image size
     *
     * @param ImageSizeBase $size
     * @return void
     */
    public function addSize(string $size) : void
    {
        $this->sizes[] = $size;
    }

    public function get(int $postId)
    {
        foreach ($this->images as $images) {
            if ($images->postId = $postId) {
                return $images;
            }
        }

        return new FeaturedImages($postId);
    }

    public function list() : array
    {
        $list = [];
        foreach ($this->images as $images) {
            $list[$images->postId] = $images->list();
        }

        return $list;
    }

    private function populateImages(array $results)
    {
        foreach ($results as $result) {
            $postId = $result->post_id;
            
            $featuredImages = new FeaturedImages($postId);
            $featuredImages->add($this->sizes, $result);
            $this->images[] = $featuredImages;
        }
    }

    public function fetch(array $postIds)
    {
        global $wpdb;

        $this->images = [];

        $postList = join(',', $postIds);

        $query = "SELECT
            pm1.post_id,
            pm1.meta_value AS attachment_id,
            pm2.meta_value AS attachment_metadata,
            pm3.meta_value AS alt,
            p.post_title AS title,
            p.post_content AS description,
            p.post_excerpt AS caption
        FROM $wpdb->postmeta pm1
        LEFT JOIN $wpdb->postmeta pm2 ON pm1.meta_value = pm2.post_id AND pm2.meta_key = '_wp_attachment_metadata'
        LEFT JOIN $wpdb->postmeta pm3 ON pm1.meta_value = pm3.post_id AND pm3.meta_key = '_wp_attachment_image_alt'
        INNER JOIN $wpdb->posts p ON pm1.meta_value = p.ID
        WHERE pm1.post_id IN ($postList)
        AND pm1.meta_key = '_thumbnail_id'";

        $results = $wpdb->get_results($query);
        if ($results === false) {
            return new \WP_Error(
                'fetch_featured_images_failed',
                __('Sorry, fetching featured images failed.', 'peteklein'),
                [
                    'post_ids' => $postIds
                ]
            );
        }

        $this->populateImages($results);
        
        return $this->list();
    }
}
