<?php

namespace PeteKlein\Performant\Posts\Meta;

use Carbon_Fields\Container;

class PostMetaBox
{
    private $container;

    public function __construct(string $postType, string $label)
    {
        $this->container = Container::make('post_meta', $label)
            ->where('post_type', '=', $postType);
    }

    public function addAdminField($field)
    {
        $this->container->add_fields([$field]);

        return $this;
    }
}
