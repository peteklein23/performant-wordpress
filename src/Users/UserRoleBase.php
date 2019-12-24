<?php

namespace PeteKlein\Performant\Users;

use PeteKlein\Performant\Patterns\Singleton;

abstract class UserRoleBase extends Singleton
{
    /**
     * post type slug to be overridden in inheriting class
     */
    const ROLE = '';

    protected static $instances = [];
    private $label = '';
    private $capabilities = [];

    public function __construct(string $label, array $capabilities = [])
    {
        if (empty(static::ROLE)) {
            throw new \Exception(__('You must set the constant ROLE to inherit from UserRoleBase', 'performant'));
        }

        $this->label = $label;
        $this->capabilities = $capabilities;
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
}
