<?php

/**
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */

namespace Marketplace;

use SimpleORMap;
use \Marketplace\Category;
use \Marketplace\Demand;
use \Marketplace\CategoryNotification;

class CategoryDemand extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'mp_category_demand';
        $config['belongs_to']['mp_category'] = [
            'class_name' => \Marketplace\Category::class,
            'foreign_key' => 'category_id',
            'assoc_foreign_key' => 'id'
        ];
        $config['belongs_to']['mp_demand'] = [
            'class_name' => \Marketplace\Demand::class,
            'foreign_key' => 'demand_id',
            'assoc_foreign_key' => 'id'
        ];
        parent::configure($config);
    }


    public static function get_saved_path($demand_id)
    {

        $root_category = CategoryDemand::findBySQL("LEFT JOIN mp_category ON mp_category_demand.category_id = mp_category.id WHERE mp_category.parent_category_id IS NULL AND demand_id = ?", [$demand_id])[0];
        $path = "" . $root_category->mp_category->name;

        $current_category = $root_category;
        while ($category = CategoryDemand::findBySQL("LEFT JOIN mp_category ON mp_category_demand.category_id = mp_category.id WHERE mp_category.parent_category_id = ? AND demand_id = ?", [$current_category->mp_category->id, $demand_id])[0]) {
            $path .= "/" . $category->mp_category->name;
            $current_category = $category;
        }

        return $path;
    }

    function set_category_demand($categories, $demand_id)
    {
        $old_categories = self::findBySQL("demand_id = ?", [$demand_id]);
        $new_categories_obj = [];
        $i = 0;
        foreach ($categories as $category) {
            $new_categories_obj[$i] = new CategoryDemand();
            $new_categories_obj[$i]->category_id = $category["id"];
            $new_categories_obj[$i]->demand_id = $demand_id;
            $i++;
        }

        $to_insert = array_udiff($new_categories_obj, $old_categories, function ($a, $b) {
            return strcmp($a->category_id, $b->category_id);
        });

        $to_delete = array_udiff($old_categories, $new_categories_obj, function ($a, $b) {
            return strcmp($a->category_id, $b->category_id);
        });

        foreach ($to_insert as $category) {
            $category->store();

            $users = CategoryNotification::getUserIDsByCategory($category->category_id);

            \PersonalNotifications::add(
                $users, //id of user A or array of 'multiple user_ids
                \PluginEngine::getLink('marketplace/overview/demand_detail/' . $demand_id), //when user A clicks this URL he/she should jump directly to the changed wiki-page
                "New demand with category: '" . $category->mp_category->name  . "'", //a small text that describes the notification
                "",
                \Icon::create("wiki", "clickable"),
                true
            );
        }

        foreach ($to_delete as $category) {
            $category->delete();
        }
    }
}
