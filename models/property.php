<?php

namespace Marketplace;

use SimpleORMap;

class Property extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'mp_property';
        $config['belongs_to']['mp_demand'] = [
            'class_name' => \Marketplace\Demand::class,
            'foreign_key' => 'demand_id',
            'assoc_foreign_key' => 'id'
        ];
        parent::configure($config);
    }
}
