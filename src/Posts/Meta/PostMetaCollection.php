<?php

namespace PeteKlein\Performant\Posts\Meta;

use PeteKlein\Performant\Fields\FieldBase;

/**
 * Gets and references metadata across multiple posts
 */
class PostMetaCollection
{
    private $fields = [];
    private $metaResults = [];

    /**
     * * Add field to list of fields
     *
     * @param FieldBase $field - the field to add
     */
    public function addField(FieldBase $field) : PostMetaCollection
    {
        $this->fields[] = $field;

        return $this;
    }

    /**
     * Get a field by field key
     *
     * @param string $key - key of the field to retrieve
     */
    public function getField(string $key) : ?FieldBase
    {
        foreach ($this->fields as $field) {
            if ($field->key === $key) {
                return $field;
            }
        }

        return null;
    }

    public function list() : array
    {
        $formattedList = [];
        foreach ($this->metaResults as $meta) {
            $formattedList[$meta->postId] = $meta->list();
        }

        return $formattedList;
    }

    public function get(int $postId)
    {
        foreach ($this->metaResults as $meta) {
            if ($meta->postId === $postId) {
                return $meta;
            }
        }

        return null;
    }

    private function getWhereClause() : string
    {
        $fieldCount = count($this->fields);
        $sql = 'WHERE ';
        foreach($this->fields as $i => $field){
            $isLast = $i === $fieldCount - 1;
            $sql .= 'meta_key ' . $field->getSelectionSQL();
            if(!$isLast) {
                $sql .= ' OR ';
            }
        }

        return $sql;
    }

    public function getValue(int $postId, string $key)
    {
        $post = null;
        if (!empty($this->metaResults[$postId])) {
            $post = $this->metaResults[$postId];
        }

        if (!empty($post[$key])) {
            return $post[$key];
        }

        return null;
    }

    private function hasFields() : bool
    {
        return !empty($this->fields);
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

    private function populateMetaFromResults(array $results) : void
    {
        $groupedResults = $this->groupById($results);
        
        foreach ($this->fields as $field) {
            foreach($groupedResults as $postId => $meta) {
                $postMeta = $this->get($postId);
                if(empty($postMeta)){
                    $postMeta = new PostMeta($postId);
                    $this->metaResults[] = $postMeta;
                }
                $value = $field->getValue($meta);
                $postMeta->add($field->key, $value);
            }
        }
    }

    public function fetch(array $postIds) : void
    {
        global $wpdb;

        // empty meta list
        $this->metaResults = [];

        if (!$this->hasFields()) {
            return;
        }

        $postList = join(',', $postIds);
        $whereClause = $this->getWhereClause();

        $query = "SELECT 
            * 
        FROM $wpdb->postmeta
        $whereClause
        AND post_id IN ($postList)";

        $results = $wpdb->get_results($query);
        if ($results === false) {
            // TODO: throw meta exception
            new \WP_Error(
                'fetch_post_meta_failed',
                __('Sorry, fetching the post meta failed.', 'peteklein'),
                [
                    'post_ids' => $postIds,
                    'fields' => $this->fields
                ]
            );
        }

        $this->populateMetaFromResults($results);
    }

    private function flattenColumns(array $columns, bool $unique)
    {
        $columnValues = [];
        foreach ($columns as $column) {
            $columnValues = array_merge($columnValues, $column);
        }
        if (!$unique) {
            return $columnValues;
        }

        return array_unique($columnValues);
    }

    public function listColumn(string $key, bool $flatten = false, $unique = false)
    {
        $columns = [];
        foreach ($this->metaResults as $meta) {
            $columns[] = $meta->get($key);
        }

        if (!$flatten) {
            return $columns;
        }

        return $this->flattenColumns($columns, $unique);
    }
}
