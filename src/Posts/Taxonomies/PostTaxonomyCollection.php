<?php

namespace PeteKlein\Performant\Posts\Taxonomies;

use PeteKlein\Performant\Taxonomies\TaxonomyBase;

class PostTaxonomyCollection
{
    private $taxonomies = [];
    private $terms = [];

    /**
     * Add a taxonomy to the list of taxonomies
     *
     * @param TaxonomyBase $taxonomy
     */
    public function addTaxonomy(TaxonomyBase $taxonomy) : PostTaxonomyCollection
    {
        $this->taxonomies[] = $taxonomy;

        return $this;
    }

    /**
     * Returns if this class has any taxonomies
     */
    private function hasTaxonomies() : bool
    {
        return !empty($this->taxonomies);
    }

    /**
     * Return an array of taxonomies
     */
    private function listTaxonomySlugs() : array
    {
        $slugs = [];
        foreach($this->taxonomies as $taxonomy) {
            $slugs[] = $taxonomy::TAXONOMY;
        }

        return $slugs;
    }

    /**
     * Return a the PostTaxonomies object for a post
     *
     * @param integer $postId
     */
    public function get(int $postId) : ?PostTaxonomies
    {
        if (!empty($this->terms[$postId])) {
            return $this->terms[$postId]->list();
        }

        return null;
    }

    /**
     * Return a formatted list of all terms
     */
    public function list() : array
    {
        $formattedList = [];
        foreach ($this->terms as $postId => $postTaxonomies) {
            $formattedList[$postId] = $postTaxonomies->list();
        }

        return $formattedList;
    }

    /**
     * Groups results by object id
     *
     * @param array $results
     */
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
    
    /**
     * Populates Taxonomies with results
     *
     * @param array $results
     * @return void
     */
    private function populateTaxonomiesFromResults(array $results) : void
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
