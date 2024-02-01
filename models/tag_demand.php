<?php

namespace Marketplace;

use SimpleORMap;

class TagDemand extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'mp_tag_demand';
        $config['belongs_to']['mp_demand'] = [
            'class_name' => \Marketplace\Demand::class,
            'foreign_key' => 'demand_id',
            'assoc_foreign_key' => 'id'
        ];
        $config['belongs_to']['mp_tag'] = [
            'class_name' => \Marketplace\Tag::class,
            'foreign_key' => 'tag_id',
            'assoc_foreign_key' => 'id'
        ];
        parent::configure($config);
    }

    public function addTag($tag_name, $demand_id)
    {
        $tag_obj = \Marketplace\Tag::findByName($tag_name);
        if (!$tag_obj) {
            $tag_obj = new \Marketplace\Tag();
            $tag_obj->name = $tag_name;
            $tag_obj->store();
        }
        $result = TagDemand::findOneBySQL("tag_id = ? AND demand_id = ?", [$tag_obj->id, $demand_id]);
        if (!$result) {
            $tag_demand_obj = new \Marketplace\TagDemand();
            $tag_demand_obj->demand_id = $demand_id;
            $tag_demand_obj->tag_id = $tag_obj->id;
            $tag_demand_obj->store();
        }
    }
}
