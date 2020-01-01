<?php

namespace PeteKlein\Performant\Posts\Meta;

use PeteKlein\Performant\Meta\MetaCollection;

class PostMetaCollection extends MetaCollection
{
    public function __construct()
    {
        global $wpdb;

        parent::__construct($wpdb->postmeta, 'post_id');
    }
}
