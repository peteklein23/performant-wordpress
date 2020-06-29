<?php
namespace PeteKlein\Performant\Components;

use PeteKlein\Performant\Patterns\Singleton;

abstract class ComponentBase extends Singleton
{
    const COMPONENT_NAME = null;

    abstract public function render($data): void;

    protected static $instances = [];

    public function __construct()
    {
        if (empty(static::COMPONENT_NAME)) {
            throw new \Exception(
                'The constant COMPONENT_NAME must be set on classes inheriting from ComponentBase'
            );
        }
    }

    /**
     * @inheritDoc
     *
     * @return ComponentBase
     */
    public static function getInstance(): ComponentBase
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }

        return self::$instances[$cls];
    }

    protected function registerStyle(
        string $src,
        array $dependencies = [],
        string $version = null,
        bool $inFooter = false
    ) {
        wp_enqueue_style(
            static::COMPONENT_NAME,
            $src,
            $dependencies,
            $version,
            $inFooter
        );
    }

    protected function registerScript(
        string $src,
        array $dependencies = [],
        string $version = null,
        bool $inFooter = false
    ) {
        wp_enqueue_script(
            static::COMPONENT_NAME,
            $src,
            $dependencies,
            $version,
            $inFooter
        );
    }
}
