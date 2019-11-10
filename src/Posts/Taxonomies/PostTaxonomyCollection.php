<?php

namespace PeteKlein\Performant\Posts\Taxonomies;

use PeteKlein\Performant\Taxonomies\TaxonomyBase;

class PostTaxonomyCollection
{
    private $taxonomies = [];
    private $terms = [];

    public function addTaxonomy(TaxonomyBase $taxonomy) : PostTaxonomyCollection
    {
        $this->taxonomies[] = $taxonomy;

        return $this;
    }

    private function hasTaxonomies() : bool
    {
        return !empty($this->taxonomies);
    }

    private function listTaxonomySlugs() : array
    {
        $slugs = [];
        foreach($this->taxonomies as $taxonomy) {
            $slugs[] = $taxonomy::TAXONOMY;
        }

        return $slugs;
    }

    public function get(int $postId)
    {
        foreach ($this->terms as $postTaxonomies) {
            if ($postTaxonomies->postId === $postId) {
                return $postTaxonomies;
            }
        }

        return null;
    }

    public function list()
    {
        $formatted_list = [];
        foreach ($this->terms as $postTaxonomies) {
            $formatted_list[$postTaxonomies->postId] = $postTaxonomies->list();
        }

        return $formatted_list;
    }

    private function groupById(array $results) : array
    {
        $groupedResults = [];

        foreach ($results as $result) {
            $postId = $result->post_id;

            if (empty($groupedResults[$postId])) {
                $groupedResults[$postId] = [];
            }

            $groupedResults[$postId][] = $result;
        }

        return $groupedResults;
    }
    
    private function populateTaxonomiesFromResults($results) : void
    {
        $groupedResults = $this->groupById($results);

        foreach ($groupedResults as $postId => $terms) {
            $postTaxonomies = new PostTaxonomies($postId);
            foreach($terms as $term) {
                $typedTerm = new \WP_Term($term);
                $postTaxonomies->add($typedTerm);
            }
            
            $this->terms[$postId] = $postTaxonomies;
        }
    }

    public function fetch(array $postIds) : void
    {
        global $wpdb;

        $this->terms = [];
        
        if (!$this->hasTaxonomies()) {
            return;
        }

        $taxonomySlugs = $this->listTaxonomySlugs();
        $terms = "'" . join("', '", $taxonomySlugs) . "'";
        $postList = join(', ', $postIds);

        $query = "SELECT
            tr.object_id as post_id,
            tt.term_id,
            t.name,
            t.slug,
            t.term_group,
            tt.term_taxonomy_id,
            tt.taxonomy,
            tt.description,
            tt.parent,
            tt.count
        FROM $wpdb->term_relationships tr
        INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        INNER JOIN $wpdb->terms AS t ON t.term_id = tt.term_id
        WHERE tt.taxonomy IN ($terms)
            AND tr.object_id IN ($postList)";

        $results = $wpdb->get_results($query);

        $this->populateTaxonomiesFromResults($results);
    }
}
