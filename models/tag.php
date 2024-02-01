<?php

namespace Marketplace;

use SimpleORMap;

class Tag extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'mp_tag';
        $config['has_many']['mp_tag_demand'] = [
            'class_name' => \Marketplace\TagDemand::class,
            'assoc_func' => 'findByParent_id'
        ];
        parent::configure($config);
    }

    public function findByName($name): ?Tag
    {
        return \Marketplace\Tag::findOneBySQL("name = ?", [$name]);
    }
}
