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
}
