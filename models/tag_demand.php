<?php

namespace Marketplace;

use SimpleORMap;

class TagDemand extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'tag_demand';
        $config['belongs_to']['demand'] = [
            'class_name' => \Marketplace\Demand::class,
            'foreign_key' => 'demand_id',
            'assoc_foreign_key' => 'id'
        ];
        $config['belongs_to']['tag'] = [
            'class_name' => \Marketplace\Tag::class,
            'foreign_key' => 'tag_id',
            'assoc_foreign_key' => 'id'
        ];
        parent::configure($config);
    }
}
