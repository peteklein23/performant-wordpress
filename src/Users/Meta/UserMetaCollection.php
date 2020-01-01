<?php

namespace PeteKlein\Performant\Users\Meta;

use PeteKlein\Performant\Meta\MetaCollection;

class UserMetaCollection extends MetaCollection
{
    public function __construct()
    {
        global $wpdb;

        parent::__construct($wpdb->usermeta, 'user_id');
    }
}
