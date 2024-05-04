<?php

/**
 *  Controller for global configuration of catalog
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */


use Marketplace\TagDemand;
use Marketplace\SqlGenerator;
use \Marketplace\CustomProperty;
use \Marketplace\MarketplaceModel;
use \Marketplace\Tag;

class GlobalConfigController extends \Marketplace\Controller
{

    public function index_action()
    {
        if ($GLOBALS['user']->perms != 'root' && $GLOBALS['user']->perms != 'admin') {
            PageLayout::postError('You do not have permission to access this page!');
        }
        Navigation::activateItem('marketplace_root/global_config/general');
        PageLayout::setTitle('Configuration');
    }

    public function save_config_action()
    {
        if ($GLOBALS['user']->perms != 'root' && $GLOBALS['user']->perms != 'admin') {
            PageLayout::postError('You do not have permission to access this page!');
        }
        $config = json_decode(file_get_contents('php://input'), true);
        MarketplaceModel::update_marketplaces($config["marketplaces"]);
        Tag::update_tags($config["tags"]);
        $db = DBManager::get();
        $old_marketplaces = $db->fetchAll("SELECT * FROM mp_marketplace");
        PageLayout::postSuccess('Configuration was saved successfully.');
        $this->render_text('' . json_encode($old_marketplaces));
    }

    public function post_success_action()
    {
        echo MessageBox::success('Message', ['optional details'], true);
        $this->render_nothing();
    }

    public function get_tags_action()
    {
        if ($GLOBALS['user']->perms != 'root' && $GLOBALS['user']->perms != 'admin') {
            PageLayout::postError('You do not have permission to access this page!');
        }
        $db = DBManager::get();
        //count number of references
        $tags = $db->fetchAll("SELECT mp_tag.id AS id, mp_tag.name AS name, COUNT(mp_tag_demand.demand_id) AS number_of_references FROM mp_tag LEFT JOIN mp_tag_demand ON mp_tag.id = mp_tag_demand.tag_id GROUP BY mp_tag.id");
        $this->render_text('' . json_encode($tags));
    }

    public function get_config_action()
    {
        if ($GLOBALS['user']->perms != 'root' && $GLOBALS['user']->perms != 'admin') {
            PageLayout::postError('You do not have permission to access this page!');
        }
        $db = DBManager::get();
        $old_marketplaces = $db->fetchAll("SELECT * FROM mp_marketplace");
        $this->render_text('' . json_encode($old_marketplaces));
    }

    public function delete_unused_tags_action()
    {
        if ($GLOBALS['user']->perms != 'root' && $GLOBALS['user']->perms != 'admin') {
            PageLayout::postError('You do not have permission to access this page!');
        }
        $db = DBManager::get();
        $db->execute("DELETE FROM mp_tag WHERE id NOT IN (SELECT tag_id FROM mp_tag_demand)");
        $this->render_text('' . "Tags deleted successfully");
    }

    public function export_action()
    {
        if ($GLOBALS['user']->perms != 'root' && $GLOBALS['user']->perms != 'admin') {
            PageLayout::postError('You do not have permission to access this page!');
        }
        Navigation::activateItem('marketplace_root/global_config/export');
        PageLayout::setTitle('Configuration');
    }

    public function export_users_action()
    {
        if ($GLOBALS['user']->perms != 'root' && $GLOBALS['user']->perms != 'admin') {
            PageLayout::postError('You do not have permission to export data!');
        }
        $db = DBManager::get();
        $users = $db->fetchAll("SELECT * FROM auth_user_md5");
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename=users.json');
        header('Pragma: no-cache');
        $this->render_text('' . json_encode($users));
    }

    public function export_data_action()
    {
        if ($GLOBALS['user']->perms != 'root' && $GLOBALS['user']->perms != 'admin') {
            PageLayout::postError('You do not have permission to export data');
        }
        //load all data from database
        $db = DBManager::get();
        $marketplaces = $db->fetchAll("SELECT * FROM mp_marketplace");
        $tags = $db->fetchAll("SELECT * FROM mp_tag");
        $demands = $db->fetchAll("SELECT * FROM mp_demand");
        $tag_demands = $db->fetchAll("SELECT * FROM mp_tag_demand");
        $demand_property = $db->fetchAll("SELECT * FROM mp_demand_property");
        $custom_properties = $db->fetchAll("SELECT * FROM mp_custom_property");
        $properties = $db->fetchAll("SELECT * FROM mp_property");
        $bookmarks = $db->fetchAll("SELECT * FROM mp_bookmark");
        $tag_notifications = $db->fetchAll("SELECT * FROM mp_tag_notification");
        $search_notifications = $db->fetchAll("SELECT * FROM mp_search_notification");
        $search_demands = $db->fetchAll("SELECT * FROM mp_search_demand");
        $categories = $db->fetchAll("SELECT * FROM mp_category");
        $category_demands = $db->fetchAll("SELECT * FROM mp_category_demand");

        $data_object = [
            "marketplaces" => $marketplaces,
            "tags" => $tags,
            "demands" => $demands,
            "tag_demands" => $tag_demands,
            "demand_property" => $demand_property,
            "custom_properties" => $custom_properties,
            "properties" => $properties,
            "bookmarks" => $bookmarks,
            "tag_notifications" => $tag_notifications,
            "search_notifications" => $search_notifications,
            "search_demands" => $search_demands,
            "categories" => $categories,
            "category_demands" => $category_demands
        ];
        // headers for file download
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename=marketplaces.json');
        header('Pragma: no-cache');
        $this->render_text('' . json_encode($data_object));
    }


    public function import_data_action()
    {
        if ($GLOBALS['user']->perms != 'root' && $GLOBALS['user']->perms != 'admin') {
            PageLayout::postError('You do not have permission to access this page!');
        }
        self::import_data(json_decode(file_get_contents($_FILES["backup"]["tmp_name"]), true));
        Navigation::activateItem('marketplace_root/global_config/export');
        PageLayout::setTitle('Configuration');
    }

    public function import_data($data)
    {
        //clear tables
        $db = DBManager::get();
        $db->execute("DELETE FROM mp_marketplace");
        $db->execute("DELETE FROM mp_tag");
        $db->execute("DELETE FROM mp_demand");
        $db->execute("DELETE FROM mp_tag_demand");
        $db->execute("DELETE FROM mp_demand_property");
        $db->execute("DELETE FROM mp_custom_property");
        $db->execute("DELETE FROM mp_property");
        $db->execute("DELETE FROM mp_bookmark");
        $db->execute("DELETE FROM mp_tag_notification");
        $db->execute("DELETE FROM mp_search_notification");
        $db->execute("DELETE FROM mp_search_demand");
        $db->execute("DELETE FROM mp_category");
        $db->execute("DELETE FROM mp_category_demand");

        //load data from file

        $marketplaces = $data["marketplaces"];
        $tags = $data["tags"];
        $demands = $data["demands"];
        $tag_demands = $data["tag_demands"];
        $demand_property = $data["demand_property"];
        $custom_properties = $data["custom_properties"];
        $properties = $data["properties"];
        $bookmarks = $data["bookmarks"];
        $tag_notifications = $data["tag_notifications"];
        $search_notifications = $data["search_notifications"];
        $search_demands = $data["search_demands"];
        $categories = $data["categories"];
        $category_demands = $data["category_demands"];

        //insert data into database
        foreach ($marketplaces as $marketplace) {
            $db->execute("INSERT INTO mp_marketplace (id, name,enabled, comodity_name_singular, comodity_name_plural) VALUES (?, ?, ?,?, ?)", [$marketplace["id"], $marketplace["name"], $marketplace["enabled"], $marketplace["comodity_name_singular"], $marketplace["comodity_name_plural"]]);
        }
        foreach ($tags as $tag) {
            $db->execute("INSERT INTO mp_tag (id, name) VALUES (?, ?)", [$tag["id"], $tag["name"]]);
        }
        foreach ($demands as $demand) {
            $db->execute("INSERT INTO mp_demand (id, title, description, author_id, marketplace_id, mkdate, chdate) VALUES (?, ?, ?, ?, ?, ?, ?)", [$demand["id"], $demand["title"], $demand["description"], $demand["author_id"], $demand["marketplace_id"], $demand["mkdate"], $demand["chdate"]]);
        }
        foreach ($tag_demands as $tag_demand) {
            $db->execute("INSERT INTO mp_tag_demand (id, tag_id, demand_id) VALUES (?, ?, ?)", [$tag_demand["id"], $tag_demand["tag_id"], $tag_demand["demand_id"]]);
        }
        foreach ($demand_property as $property) {
            $db->execute("INSERT INTO mp_demand_property (id, demand_id, property_id, value) VALUES (?, ?, ?, ?)", [$property["id"], $property["demand_id"], $property["property_id"], $property["value"]]);
        }
        foreach ($custom_properties as $custom_property) {
            $db->execute("INSERT INTO mp_custom_property (id, name, type, required, marketplace_id, order_index) VALUES (?, ?, ?, ?, ?, ?)", [$custom_property["id"], $custom_property["name"], $custom_property["type"], $custom_property["required"], $custom_property["marketplace_id"], $custom_property["order_index"]]);
        }
        foreach ($properties as $property) {
            $db->execute("INSERT INTO mp_property (id, demand_id, custom_property_id, value) VALUES (?, ?, ?, ?)", [$property["id"], $property["demand_id"], $property["custom_property_id"], $property["value"]]);
        }
        foreach ($bookmarks as $bookmark) {
            $db->execute("INSERT INTO mp_bookmark (id, author_id, demand_id) VALUES (?, ?, ?)", [$bookmark["id"], $bookmark["author_id"], $bookmark["demand_id"]]);
        }
        foreach ($tag_notifications as $tag_notification) {
            $db->execute("INSERT INTO mp_tag_notification (id, author_id, tag_id) VALUES (?, ?, ?)", [$tag_notification["id"], $tag_notification["author_id"], $tag_notification["tag_id"]]);
        }
        foreach ($search_notifications as $search_notification) {
            $db->execute("INSERT INTO mp_search_notification (id, author_id, search_query, marketplace_id) VALUES (?, ?, ?)", [$search_notification["id"], $search_notification["author_id"], $search_notification["search_string"]]);
        }
        foreach ($search_demands as $search_demand) {
            $db->execute("INSERT INTO mp_search_demand (id, demand_id, search_notification_id) VALUES (?, ?, ?)", [$search_demand["id"], $search_demand["author_id"], $search_demand["demand_id"]]);
        }
        //ignore temporary foreign key checks
        $db->execute("SET FOREIGN_KEY_CHECKS=0");
        foreach ($categories as $category) {
            $db->execute("INSERT INTO mp_category (id, name, parent_category_id, marketplace_id) VALUES (?, ?, ?, ?)", [$category["id"], $category["name"], $category["parent_category_id"], $category["marketplace_id"]]);
        }
        $db->execute("SET FOREIGN_KEY_CHECKS=1");
        foreach ($category_demands as $category_demand) {
            $db->execute("INSERT INTO mp_category_demand (id, category_id, demand_id) VALUES (?, ?, ?)", [$category_demand["id"], $category_demand["category_id"], $category_demand["demand_id"]]);
        }
        PageLayout::postSuccess('Data imported successfully');
        $this->redirect('global_config/export/');
    }
}
