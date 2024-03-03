<?php

namespace Marketplace;

use SimpleORMap;

class SearchNotification extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'mp_search_notification';
        $config['belongs_to']['author'] = [
            'class_name' => User::class,
            'foreign_key' => 'author_id',
            'assoc_foreign_key' => 'user_id'
        ];
        $config['has_many']['mp_search_demand'] = [
            'class_name' => \Marketplace\SearchDemand::class,
            'assoc_func' => 'findByParent_id'
        ];
        $config['belongs_to']['mp_marketplace'] = [
            'class_name' => \Marketplace\MarketplaceModel::class,
            'foreign_key' => 'marketplace_id',
            'assoc_foreign_key' => 'id'
        ];
        parent::configure($config);
    }

    public static function getSubscribedSearches($user_id)
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

    public static function subscribeToSearch($user_id, $search, $demands, $marketplace_id)
    {
        //check if user already saved this search
        $check = self::findBySQL("author_id = ? AND search_query = ?", [$user_id, $search]);
        if ($check) {
            return;
        }
        $search_notification = new SearchNotification();
        $search_notification->author_id = $user_id;
        $search_notification->search_query = $search;
        $search_notification->marketplace_id = $marketplace_id;
        $search_notification->store();
        foreach ($demands as $demand) {
            $search_demand = new \Marketplace\SearchDemand();
            $search_demand->parent_id = $search_notification->id;
            $search_demand->demand_id = $demand;
            $search_demand->store();
        }
    }

    public static function setSearchNotifications($new_searches, $user_id)
    {
        $current_searches = self::getSubscribedSearches($user_id);
        $current_searches = array_map(function ($search) {
            return $search->search_query;
        }, $current_searches);

        $searches_to_remove = array_udiff($current_searches, $new_searches, function ($a, $b) {
            return strcmp($a, $b);
        });

        foreach ($searches_to_remove as $search) {
            $search_notification = self::findOneBySQL("author_id = ? AND search_query = ?", [$user_id, $search]);
            $search_notification->delete();
        }
    }

    public static function notifyUsers()
    {
        //TODO:
    }
}
