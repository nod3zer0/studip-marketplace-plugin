<?php

/**
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */

namespace Marketplace;

use SimpleORMap;

class Bookmark extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'mp_bookmark';
        $config['belongs_to']['mp_demand'] = [
            'class_name' => \Marketplace\Demand::class,
            'foreign_key' => 'demand_id',
            'assoc_foreign_key' => 'id'
        ];
        $config['belongs_to']['author'] = [
            'class_name' => User::class,
            'foreign_key' => 'author_id',
            'assoc_foreign_key' => 'user_id'
        ];
        parent::configure($config);
    }
    /**
     * Set bookmark for demand
     * @param $demand_id
     * @param $author_id
     * @param $bookmark_status
     */
    public function setBookmark($demand_id, $author_id, $bookmark_status)
    {
        $bookmark = self::findOneBySQL("demand_id = ? AND author_id = ?", [$demand_id, $author_id]);
        if ($bookmark_status) {
            if (!$bookmark) {
                $bookmark = new Bookmark();
                $bookmark->demand_id = $demand_id;
                $bookmark->author_id = $author_id;
                $bookmark->store();
            }
        } else {
            if ($bookmark) {
                $bookmark->delete();
            }
        }
    }

    /**
     * Add bookmark for demand
     * @param $demand_id
     * @param $author_id
     */
    public function addBookmark($demand_id, $author_id)
    {
        $bookmark = new Bookmark();
        $bookmark->demand_id = $demand_id;
        $bookmark->author_id = $author_id;
        $bookmark->store();
    }

    /**
     * Remove bookmark for demand
     * @param $demand_id
     * @param $author_id
     */
    public function removeBookmark($demand_id, $author_id)
    {
        $bookmark = self::findOneBySQL("demand_id = ? AND author_id = ?", [$demand_id, $author_id]);
        $bookmark->delete();
    }

    /**
     * Get all bookmarks for a given user in a given marketplace
     * @param $author_id
     * @return array
     */
    public function getByMarketplace($marketplace_id, $author_id)
    {
        return \Marketplace\Demand::findBySQL("RIGHT JOIN mp_bookmark ON mp_bookmark.demand_id = mp_demand.id WHERE mp_demand.marketplace_id = ? AND mp_bookmark.author_id = ?", [$marketplace_id, $author_id]);
    }

    /**
     * Get bookmark by demand and author
     * @param $demand_id
     * @param $author_id
     * @return Bookmark
     */
    public function getBookmarkByDemand($demand_id, $author_id)
    {
        return self::findOneBySQL("demand_id = ? AND author_id = ?", [$demand_id, $author_id]);
    }

    /**
     * Get all bookmarks for a given user
     * @param $author_id
     * @return array
     */
    public function getAllBookmarks($author_id)
    {
        return \Marketplace\Demand::findBySQL("RIGHT JOIN mp_bookmark ON mp_bookmark.demand_id = mp_demand.id WHERE mp_bookmark.author_id = ?", [$author_id]);
    }
}
