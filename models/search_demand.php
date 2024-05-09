<?php

/**
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */

namespace Marketplace;

use SimpleORMap;

class SearchDemand extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'mp_search_notification';
        $config['belongs_to']['mp_search_notification'] = [
            'class_name' => \Marketplace\SearchDemand::class,
            'foreign_key' => 'author_id',
            'assoc_foreign_key' => 'user_id'
        ];
        $config['belongs_to']['mp_demand'] = [
            'class_name' => \Marketplace\Demand::class,
            'foreign_key' => 'author_id',
            'assoc_foreign_key' => 'user_id'
        ];
        parent::configure($config);
    }

    /**
     * Get all searches for a given user (unused)
     * @param $user_id
     * @return array
     */
    public static function getSubscribedSearches($user_id)
    {
        return self::findBySQL("author_id = ?", [$user_id]);
    }
}
