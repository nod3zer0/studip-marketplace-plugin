<?php

/**
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */

namespace Marketplace;

use SimpleORMap;
use \Marketplace\MarketplaceModel;

class CategoryNotification extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'mp_category_notification';
        $config['belongs_to']['mp_category'] = [
            'class_name' => \Marketplace\Category::class,
            'foreign_key' => 'category_id',
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
     * Get all user IDs subscribed to category
     * @param $category_id
     * @return array
     */
    public static function getUserIDsByCategory($category_id)
    {
        $category_notifications = self::findBySQL("category_id = ?", [$category_id]);
        return array_map(function ($category_notification) {
            return $category_notification->author_id;
        }, $category_notifications);
    }
    /**
     * Get categories to which user is subscribed
     * @param $user_id
     * @return array
     */
    public static function getSubscribedCategories($user_id)
    {
        $marketplaces = MarketplaceModel::findBySQL("1");
        $categories = [];
        foreach ($marketplaces as $marketplace) {
            $tmp_categories = CategoryNotification::findBySQL("LEFT JOIN mp_category ON mp_category_notification.category_id = mp_category.id  LEFT JOIN mp_marketplace ON mp_marketplace.id = mp_category.marketplace_id WHERE  mp_marketplace.id = ? AND mp_category_notification.author_id = ?", [$marketplace->id, $user_id]);
            //prepare categories for frontend
            $categories = array_merge($categories, array_map(function ($category) {
                return $category->mp_category->id;
            }, $tmp_categories));
        }

        return $categories;
    }

    /**
     * Set categories for user
     * @param $user_id  - user id
     * @param $marketplaces - array marketplaces containing category ids
     */
    public static function setSubscribedCategoriesWithMarketplaces($user_id, $marketplaces)
    {
        $categories = [];
        //extract categories to one array
        foreach ($marketplaces as $category_ids) {
            $categories = array_merge($categories, json_decode($category_ids, true));
        }
        self::setSubscribedCategories($user_id, $categories);
    }

    /**
     * Set categories for user
     * @param $user_id  - user id
     * @param $categories - array of category ids
     */
    public static function setSubscribedCategories($user_id, $categories)
    {
        $current_categories = self::findBySQL("author_id = ?", [$user_id]);
        $current_categories = array_map(function ($category) {
            return $category->mp_category->id;
        }, $current_categories);

        $new_categories = $categories;

        $categories_to_remove = array_udiff($current_categories, $new_categories, function ($a, $b) {
            return strcmp($a, $b);
        });
        $categories_to_add = array_udiff($new_categories, $current_categories, function ($a, $b) {
            return strcmp($a, $b);
        });


        foreach ($categories_to_add as $category) {
            $category_notification = new CategoryNotification();
            $category_notification->author_id = $user_id;
            $category_notification->category_id = $category;
            $category_notification->store();
        }

        foreach ($categories_to_remove as $category) {
            $category_notification = self::findOneBySQL("author_id = ? AND category_id = ?", [$user_id, $category]);
            $category_notification->delete();
        }
    }
}
