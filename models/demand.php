<?php

namespace Marketplace;

use SimpleORMap;
use User;

class Demand extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'demand';
        $config['belongs_to']['author'] = [
            'class_name' => User::class,
            'foreign_key' => 'author_id',
            'assoc_foreign_key' => 'user_id'
        ];
        parent::configure($config);
    }
}
