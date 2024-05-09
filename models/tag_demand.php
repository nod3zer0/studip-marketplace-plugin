<?php

/**
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */

namespace Marketplace;

use SimpleORMap;
use \Marketplace\TagNotification;

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

    /**
     * Add tag to demand
     * @param $tag_name
     * @param $demand_id
     */
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

    /**
     * Get all tags for a given demand
     * @param $demand_id
     * @return array
     */
    public function getAllTags($demand_id): array
    {
        return TagDemand::findBySQL("demand_id = ?", [$demand_id]);
    }

    /**
     * Delete tag from demand
     * @param $tag_name
     * @param $demand_id
     */
    public function deleteTag($tag_name, $demand_id)
    {
        $tag_obj = \Marketplace\Tag::findByName($tag_name);
        if ($tag_obj) {
            $tag_demand_obj = TagDemand::findOneBySQL("tag_id = ? AND demand_id = ?", [$tag_obj->id, $demand_id]);
            if ($tag_demand_obj) {
                $tag_demand_obj->delete();
            }
        }
    }

    /**
     * Update tags for a given demand
     * @param $tags
     * @param $demand_id
     */
    public function updateTags($tags, $demand_id)
    {
        $old_tags = TagDemand::findBySQL("demand_id = ?", [$demand_id]);
        $new_tags_obj = [];
        $i = 0;
        foreach ($tags as $tag) {
            if ($tag["name"] == "" || $tag == null) {
                continue;
            }
            $tag_obj = \Marketplace\Tag::findByName($tag["name"]);
            if (!$tag_obj) {
                $tag_obj = new \Marketplace\Tag();
                $tag_obj->name = $tag["name"];
                $tag_obj->store();
            }
            $new_tags_obj[$i] = new \Marketplace\TagDemand();
            $new_tags_obj[$i]->demand_id = $demand_id;
            $new_tags_obj[$i]->tag_id = $tag_obj->id;
            $i++;
        }

        $to_delete = array_udiff($old_tags, $new_tags_obj, function ($a, $b) {
            return strcmp($a->tag_id, $b->tag_id);
        });
        $to_insert = array_udiff($new_tags_obj, $old_tags, function ($a, $b) {
            return strcmp($a->tag_id, $b->tag_id);
        });

        foreach ($to_delete as $tag) {
            TagDemand::find($tag->id)->delete();
        }
        foreach ($to_insert as $tag) {
            $tag->store();
            //notify users
            $users = TagNotification::getUserIDsByTag($tag->tag_id);

            \PersonalNotifications::add(
                $users, //id of user A or array of 'multiple user_ids
                \PluginEngine::getLink('marketplace/overview/demand_detail/' . $demand_id), //when user A clicks this URL he/she should jump directly to the changed wiki-page
                "New demand with tag: '" . $tag->mp_tag->name  . "'", //a small text that describes the notification
                "",
                \Icon::create("wiki", "clickable"),
                true
            );
        }
    }
}
