<?php

namespace Marketplace;

use SimpleORMap;

class TagNotification extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'mp_tag_notification';
        $config['belongs_to']['mp_tag'] = [
            'class_name' => \Marketplace\Tag::class,
            'foreign_key' => 'tag_id',
            'assoc_foreign_key' => 'id'
        ];
        $config['belongs_to']['author'] = [
            'class_name' => User::class,
            'foreign_key' => 'author_id',
            'assoc_foreign_key' => 'user_id'
        ];
        parent::configure($config);
    }

    public static function getSubscribedTags($user_id)
    {
        return self::findBySQL("author_id = ?", [$user_id]);
    }

    public static function getUserIDsByTag($tag_id)
    {
        $tag_notifications = self::findBySQL("tag_id = ?", [$tag_id]);
        return array_map(function ($tag_notification) {
            return $tag_notification->author_id;
        }, $tag_notifications);
    }

    public static function setSubscribedTags($user_id, $tags)
    {
        $current_tags = self::getSubscribedTags($user_id);
        $current_tags = array_map(function ($tag) {
            return $tag->mp_tag->id;
        }, $current_tags);

        $new_tags = array_map(function ($tag) {
            return $tag["id"];
        }, $tags);


        $tags_to_remove = array_udiff($current_tags, $new_tags, function ($a, $b) {
            return strcmp($a, $b);
        });
        $tags_to_add = array_udiff($new_tags, $current_tags, function ($a, $b) {
            return strcmp($a, $b);
        });

        foreach ($tags_to_add as $tag) {
            $tag_notification = new TagNotification();
            $tag_notification->author_id = $user_id;
            $tag_notification->tag_id = $tag;
            $tag_notification->store();
        }

        foreach ($tags_to_remove as $tag) {
            $tag_notification = self::findOneBySQL("author_id = ? AND tag_id = ?", [$user_id, $tag]);
            $tag_notification->delete();
        }
    }
}
