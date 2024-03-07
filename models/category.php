<?php

namespace Marketplace;

use SimpleORMap;

class Category extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'mp_category';
        $config['belongs_to']['mp_category'] = [
            'class_name' => \Marketplace\Category::class,
            'foreign_key' => 'parent_category_id',
            'assoc_foreign_key' => 'id'
        ];
        $config['belongs_to']['mp_marketplace'] = [
            'class_name' => \Marketplace\Marketplace::class,
            'foreign_key' => 'marketplace_id',
            'assoc_foreign_key' => 'id'
        ];
        $config['has_many']['mp_category'] = [
            'class_name' => \Marketplace\Category::class,
            'assoc_func' => 'findByParent_id'
        ];
        parent::configure($config);
    }

    public function get_categories($marketplace_id)
    {
        $categories = Category::findBySQL("marketplace_id = ?", [$marketplace_id]);

        return self::convert_categories($categories);
    }

    function convert_categories($categories, $parentId = null)
    {
        $result = [];
        foreach ($categories as $category) {
            if ($category->parent_category_id === $parentId) {
                $newCategory = [
                    "id" => $category->id,
                    "parent_id" => $category->parent_category_id,
                    "name" => $category->name,
                    "marketplace_id" => $category->marketplace_id,
                    "subcategories" => self::convert_categories($categories, $category->id)
                ];
                $result[] = $newCategory;
            }
        }
        return $result;
    }



    public function get_categories_by_parent_id($parent_id, $marketplace_id)
    {
        return Category::findBySQL("parent_category_id = ? AND marketplace_id = ?", [$parent_id, $marketplace_id]);
    }

    public function get_categories_by_marketplace_id($marketplace_id)
    {
        return Category::findBySQL("marketplace_id = ?", [$marketplace_id]);
    }


    static function update_recursively($categories, $parent_id, &$flattened_categories, $marketplace_id)
    {
        foreach ($categories as $category) {
            $id = null;
            if (isset($category['id'])) { //update
                $old_category = Category::find($category['id']);
                if ($old_category) {
                    $old_category->name = $category['name'];
                    $old_category->store();
                }
                $id = $old_category->id;
            } else { //create
                $new_category = new Category();
                $new_category->name = $category['name'];
                $new_category->parent_category_id = $parent_id;
                $new_category->marketplace_id = $marketplace_id;
                $new_category->store();
                $id = $new_category->id;
            }


            $flattened_categories[] = $category;
            if (!empty($category['subcategories'])) {
                self::update_recursively($category['subcategories'], $id, $flattened_categories, $marketplace_id);
            }
        }
    }

    public function set_categories($categories, $marketplace_id)
    {

        $old_categories = Category::findBySQL("marketplace_id = ?", [$marketplace_id]);
        // remap the old categories to array
        $old_categories = array_map(function ($category) {
            return [
                "id" => $category->id,
            ];
        }, $old_categories);

        $flattened_categories = [];
        self::update_recursively($categories, null, $flattened_categories, $marketplace_id);



        $to_delete = array_udiff($old_categories, $flattened_categories, function ($a, $b) {
            return strcmp($a["id"], $b['id']);
        });

        foreach ($to_delete as $category) {
            $cat_del = Category::find($category["id"]);
            if ($cat_del) {
                $cat_del->delete();
            }
        }
    }
}
