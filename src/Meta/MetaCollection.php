<?php

namespace PeteKlein\Performant\Meta;

use PeteKlein\Performant\Fields\FieldBase;
use PeteKlein\Performant\Meta\Meta;

/**
 * Gets and references metadata across multiple records
 */
abstract class MetaCollection
{
    private $table = '';
    private $idColumn = '';
    private $fields = [];
    private $metaResults = [];

    public function __construct(string $table, string $idColumn)
    {
        $this->table = $table;
        $this->idColumn = $idColumn;
    }

    /**
     * * Add field to list of fields
     *
     * @param FieldBase $field - the field to add
     */
    public function addField(FieldBase $field): void
    {
        $this->fields[] = $field;
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

    private function hasFields() : bool
    {
        return !empty($this->fields);
    }

    public function list() : array
    {
        $formattedList = [];
        foreach ($this->metaResults as $meta) {
            $formattedList[$meta->id] = $meta->list();
        }

        return $formattedList;
    }

    public function get(int $id)
    {
        foreach ($this->metaResults as $meta) {
            if ($meta->id === $id) {
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

    public function getValue(int $id, string $key)
    {
        $record = null;
        if (!empty($this->metaResults[$id])) {
            $record = $this->metaResults[$id];
        }

        return !empty($record[$key]) ? $record[$key] : null;
    }

    private function groupById(array $results) : array
    {
        $idColumn = $this->idColumn;
        $groupedResults = [];

        foreach ($results as $result) {
            $id = $result->$idColumn;

            if (empty($groupedResults[$id])) {
                $groupedResults[$id] = [];
            }

            $groupedResults[$id][] = $result;
        }

        return $groupedResults;
    }

    private function populateMetaFromResults(array $results): void
    {
        $groupedResults = $this->groupById($results);
        
        foreach ($this->fields as $field) {
            foreach($groupedResults as $id => $meta) {
                $m = $this->get($id);
                if (empty($m)) {
                    $m = new Meta($id);
                    $this->metaResults[] = $m;
                }
                $value = $field->getValue($meta);
                $m->add($field->key, $value);
            }
        }
    }

    public function fetch(array $ids): void
    {
        global $wpdb;

        $this->metaResults = [];

        if (!$this->hasFields()) {
            return;
        }

        $idList = join(',', $ids);
        $whereClause = $this->getWhereClause();

        $query = "SELECT 
            * 
        FROM $this->table
        $whereClause
        AND $this->idColumn IN ($idList)";

        $results = $wpdb->get_results($query);
        if ($results === false) {
            throw new \Exception(__('Sorry, fetching the meta failed.', 'peteklein'));
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
