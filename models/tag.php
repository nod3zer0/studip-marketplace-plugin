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

    public function update_tags($new_tags)
    {
        $old_tags = \Marketplace\Tag::findBySQL("1");
        $new_tags_obj = [];
        $i = 0;
        foreach ($new_tags as $tag) {
            $new_tags_obj[$i] = new \Marketplace\Tag();
            $new_tags_obj[$i]->name = $tag["name"];
            $new_tags_obj[$i]->id = $tag["id"];
            $i++;
        }

        $to_delete = array_udiff($old_tags, $new_tags_obj, function ($a, $b) {
            return strcmp($a->id, $b->id);
        });
        $to_insert = array_udiff($new_tags_obj, $old_tags, function ($a, $b) {
            return strcmp($a->id, $b->id);
        });
        $to_update = array_uintersect($new_tags_obj, $old_tags, function ($a, $b) {
            return strcmp($a->id, $b->id);
        });

        foreach ($to_delete as $tag) {
            \Marketplace\Tag::find($tag->id)->delete();
        }
        foreach ($to_insert as $tag) {
            $tag_to_insert = new \Marketplace\Tag();
            $tag_to_insert->name = $tag->name;
            $tag_to_insert->store();
        }
        foreach ($to_update as $tag) {
            $old_tag = \Marketplace\Tag::find($tag->id);
            $old_tag->name = $tag->name;
            $old_tag->store();
        }
    }
}
