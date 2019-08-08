<?php

namespace PeteKlein\Performant\Posts\Meta;

use Carbon_Fields\Toolset\Key_Toolset;
use Carbon_Fields\Field\Field;
use Carbon_Fields\Datastore\Post_Meta_Datastore;

/**
  * Prevents prefixing field keys with "_"
  */
class PostMetaCustomDatastore extends Post_Meta_Datastore {

    /**
     * Removes the prefix
     *
     * @param string $storageKey
     * @return void
     */
    private function stripPrefixFromStorageKey(string $storageKey){
        $pos = strpos($storageKey, Key_Toolset::KEY_PREFIX);

        return substr_replace($storageKey, '', $pos, strlen(Key_Toolset::KEY_PREFIX));
    }

    /**
     * Adds the prefix
     *
     * @param string $storageKey
     * @return void
     */
    private function addPrefixToStorageKey(string $storageKey){
        return Key_Toolset::KEY_PREFIX . $storageKey;
    }

    /**
     * Removes the prefix from storage key patterns
     *
     * @param array $storageKeyPatterns
     * @return void
     */
    private function stripPrefixFromStorageKeyPatterns(array $storageKeyPatterns){
        $updatedStorageKeyPatterns = [];
        foreach($storageKeyPatterns as $key => $pattern){
            $updatedKey = $this->stripPrefixFromStorageKey($key);
            $updatedStorageKeyPatterns[$updatedKey] = $pattern;
        }

        return $updatedStorageKeyPatterns;
    }

    /**
     * Adds the prefix to the cascading storage array
     *
     * @param array $cascadingStorageArray
     * @return void
     */
    private function addPrefixToCascadingStorageArray(array $cascadingStorageArray){
        $updatedArray = [];
        foreach($cascadingStorageArray as $item){
            $updatedKey = $this->addPrefixToStorageKey($item->key);
            $obj = new \stdClass();
            $obj->key = $updatedKey;
            $obj->value = $item->value;
            $updatedArray[] = $obj;
        }

        return $updatedArray;
    }
        
    /**
	 * Get the field value(s)
	 *
	 * @param Field $field The field to get value(s) for
	 * @return null|array<array>
	 */
	public function load( Field $field ) {
		$storage_key_patterns = $this->key_toolset->get_storage_key_getter_patterns( $field->is_simple_root_field(), $this->get_full_hierarchy_for_field( $field ) );
        $storage_key_patterns = $this->stripPrefixFromStorageKeyPatterns($storage_key_patterns);
        $cascading_storage_array = $this->get_storage_array( $field, $storage_key_patterns );
        $cascading_storage_array = $this->addPrefixToCascadingStorageArray($cascading_storage_array);
        $value_tree_array = $this->cascading_storage_array_to_value_tree_array( $cascading_storage_array );
		if ( $value_tree_array === null ) {
			return $value_tree_array;
		}
        $value_tree = $this->value_tree_array_to_value_tree( $value_tree_array, $field );
        
		return $value_tree;
    }
    
    /**
	 * Save the field value(s)
	 *
	 * @param Field $field The field to save.
	 */
	public function save( Field $field ) {
		$value_set = $field->get_full_value();

		if ( empty( $value_set ) && $field->get_value_set()->keepalive() ) {
			$storage_key = $this->key_toolset->get_storage_key(
				$field->is_simple_root_field(),
				$this->get_full_hierarchy_for_field( $field ),
				$this->get_full_hierarchy_index_for_field( $field ),
				0,
				$this->key_toolset->get_keepalive_property()
            );
            $storage_key = $this->stripPrefixFromStorageKey($storageKey);
			$this->save_key_value_pair( $storage_key, '' );
		}
		foreach ( $value_set as $value_group_index => $values ) {
			foreach ( $values as $property => $value ) {
				$storage_key = $this->key_toolset->get_storage_key(
					$field->is_simple_root_field(),
					$this->get_full_hierarchy_for_field( $field ),
					$this->get_full_hierarchy_index_for_field( $field ),
					$value_group_index,
					$property
                );
                $storage_key = $this->stripPrefixFromStorageKey($storage_key);
				$this->save_key_value_pair( $storage_key, $value );
			}
		}
	}
}