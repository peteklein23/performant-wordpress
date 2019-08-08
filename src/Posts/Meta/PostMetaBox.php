<?php

namespace PeteKlein\Performant\Posts\Meta;

use Carbon_Fields\Container;
use PeteKlein\Performant\Posts\Meta\PostMetaCustomDatastore;

class PostMetaBox
{
    private $container;

    public function __construct(string $postType, string $label)
    {
        $this->container = Container::make('post_meta', $label)
            ->where('post_type', '=', $postType)
            ->set_datastore( new PostMetaCustomDatastore() );
    }

    public function addField($field)
    {
        $this->container->add_fields([$field]);

        return $this;
    }
}
