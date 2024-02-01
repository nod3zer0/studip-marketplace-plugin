<?php

namespace Marketplace;

use SimpleORMap;

class Tag extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'tag';
        $config['has_many']['tag_demand'] = [
            'class_name' => \Marketplace\TagDemand::class,
            'assoc_func' => 'findByParent_id'
        ];
        parent::configure($config);
    }
}
