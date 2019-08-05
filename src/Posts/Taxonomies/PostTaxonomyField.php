<?php

namespace PeteKlein\Performant\Posts\Taxonomies;

class PostTaxonomyField
{
    public $taxonomy;
    public $default;

    public function __construct(string $taxonomy, $default)
    {
        $this->taxonomy = $taxonomy;
        $this->default = $default;
    }
}
