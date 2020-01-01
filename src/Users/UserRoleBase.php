<?php

namespace PeteKlein\Performant\Users;

use PeteKlein\Performant\Fields\FieldGroupBase;
use PeteKlein\Performant\Patterns\Singleton;
use PeteKlein\Performant\Users\Meta\UserMetaCollection;

abstract class UserRoleBase extends Singleton
{
    /**
     * role slug to be overridden in inheriting class
     */
    const ROLE = '';

    protected static $instances = [];
    private $label = '';
    private $capabilities = [];
    protected $meta;

    /**
     * Creates the container for fields in the admin
     */
    abstract public function createAdminContainer(FieldGroupBase $fieldGroup);

    public function __construct(string $label, array $capabilities = [])
    {
        if (empty(static::ROLE)) {
            throw new \Exception(__('You must set the constant ROLE to inherit from UserRoleBase', 'performant'));
        }

        $this->label = $label;
        $this->capabilities = $capabilities;

        $this->meta = new UserMetaCollection();
    }

    /**
     * @inheritDoc
     *
     * @return UserRoleBase
     */
    public static function getInstance(): UserRoleBase
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static;
        }

        return self::$instances[$cls];
    }

    /**
     * Register the user role
     *
     * @see https://codex.wordpress.org/Function_Reference/add_role
     * @return void
     */
    public function register()
    {
        $registeredPostType = add_role(static::ROLE, $this->label, $this->capabilities);

        if (is_wp_error($registeredPostType)) {
            throw new \Exception('There was an issue registering the post type.');
        }
    }

    /**
     * List users by user ids
     *
     * @param array $userIds
     * @return array
     */
    public function listUsers(array $userIds = []): array
    {
        global $wpdb;

        if (empty($userIds)) {
            return [];
        }

        $idList = join(',', $userIds);
        $likeComparison = "'%\"" . static::ROLE . "\"%'";
        $query = "SELECT
            u.id,
            u.user_login,
            u.user_nicename,
            u.user_email,
            u.user_url,
            u.user_registered,
            u.user_status,
            u.display_name
        FROM $wpdb->users u
        INNER JOIN $wpdb->usermeta um ON um.user_id = u.ID 
            AND um.meta_key = 'wp_capabilities' 
            AND um.meta_value LIKE $likeComparison
        WHERE u.ID IN($idList)";

        return $wpdb->get_results($query);
    }

    /**
     * Set field groups
     *
     * @param array $fieldGroups
     * @return void
     */
    protected function setFieldGroups(array $fieldGroups): void
    {
        if (empty($fieldGroups)) {
            return;
        }
        foreach ($fieldGroups as $fieldGroup) {
            $this->createAdminContainer($fieldGroup);
            $this->addMeta($fieldGroup);
        }
    }

    /**
     * Adds a field to the meta collection
     *
     * @param FieldGroupBase $fieldGroup
     * @return void
     */
    protected function addMeta(FieldGroupBase $fieldGroup): void
    {
        foreach ($fieldGroup->listFields() as $field) {
            $this->meta->addField($field);
        }
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
}
