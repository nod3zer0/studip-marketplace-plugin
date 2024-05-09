<?php

/**
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */

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

    /**
     * Find tag by name
     * @param $name
     * @return Tag|null
     */
    public function findByName($name): ?Tag
    {
        return \Marketplace\Tag::findOneBySQL("name = ?", [$name]);
    }

    /**
     * Get all tags in csv format
     * @param $demand_id
     * @return array
     */
    public static function get_all_tags_csv()
    {
        $tags = self::findBySQL("1");

        $tagsString = "";
        foreach ($tags as $tag) {
            $tagsString .= $tag->name . ",";
        }
        return rtrim($tagsString, ",");
    }

    /**
     * Get all tags globally
     * @param $demand_id
     * @return array
     */
    public static function get_all_tags()
    {
        return self::findBySQL("1");
    }

    /**
     * Set tags globally
     * @param $demand_id
     * @return array
     */
    public function update_tags($new_tags)
    {
        $old_tags = \Marketplace\Tag::findBySQL("1");
        $new_tags_obj = [];
        $i = 0;
        foreach ($new_tags as $tag) {
            if ($tag["name"] == "" || $tag["name"] == null) {
                continue;
            }
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
