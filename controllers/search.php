<?php

use Marketplace\SearchException;
use Marketplace\TagDemand;
use Marketplace\SqlGenerator;
use \Marketplace\SearchNotification;
use \Marketplace\Category;

class SearchController extends \Marketplace\Controller
{

    public function index_action($marketplace_id = '')
    {
        Helpbar::get()->addPlainText("Searching", "This search allows you to combine search queries with logic operators (AND, OR, NOT).
       ");
        Helpbar::get()->addPlainText("Properties", "You can search in properties by writing its name with '.' (dot) as a prefix followed by operator and searched string (eg. .description = example query), autocomplete will suggest viable options for properties and operators.");
        Helpbar::get()->addPlainText("Tags", "  Tags can be searched by specifying their names with # (hashtag) as a prefix, autocomplete will suggest available tags.");
        Helpbar::get()->addPlainText("Categories", "Categories can be searched by property .category followed by = (equal sign) and category path (eg. .category = category1/subcategory1).");
        Helpbar::get()->addPlainText("Search operators", "You can use AND, OR, NOT operators to combine queries (eg. query1 AND query2). Text properties support = (equal sign). Search searches for exact words, partial words can be searched by adding * (asterisk) in the word (eg. example*, exa*ple, etc...). Number and date properties support =, >, <, >=, <= operators (eg. .price > 100, .date >= 2021-01-01).");




        // https://github.com/nod3zer0/studip-docs-translated/blob/ba50f75faae1052d6c67a438c1c9d468f491944a/quickstart/helpbar.md
        PageLayout::addScript($this->plugin->getPluginURL() . '/assets/autocomplete.js');
        PageLayout::addStylesheet($this->plugin->getPluginURL() . '/assets/stylesheet.css');
        PageLayout::addScript($this->plugin->getPluginURL() . '/assets/bookmark_component.js');

        $this->marketplace_id = $marketplace_id;
        $marketplace_obj = \Marketplace\MarketplaceModel::find($marketplace_id);
        $this->marketplace_comodity_name_plural = $marketplace_obj->comodity_name_plural;

        Navigation::activateItem('marketplace_' . $marketplace_id . '/marketplace_search/marketplace_advanced_search_plus');
        PageLayout::setTitle(\Marketplace\MarketplaceModel::find($marketplace_id)->name);
        $db = DBManager::get();
        $query = Request::get('search-query');
        $this->query = $query;

        $custom_properties = $db->fetchAll("SELECT name, type FROM mp_custom_property WHERE marketplace_id = ?", [$marketplace_id]);
        $this->custom_properties = json_encode($custom_properties);

        $tags = $db->fetchAll("SELECT name FROM mp_tag", []);
        $this->tags = json_encode($tags);




        $categories = Category::get_categories($marketplace_id);
        $this->categories = json_encode($categories);
        $this->limit = Request::get('limit') ?: get_config('ENTRIES_PER_PAGE');
        $order = Request::get('order') ?: 'mkdate_desc';
        if ($query != '') {


            $db = DBManager::get();
            $custom_properties = $db->fetchAll("SELECT name, type FROM mp_custom_property", []);
            // $custom_properties = array_map(function ($value) {
            //     return $value['name'];
            // }, $custom_properties);


            $generator = new SqlGenerator();
            try {
                $sql = $generator->generateSQL($query, $custom_properties, $marketplace_id,  $categories, $this->limit, $order);

                $this->all_demands = \Marketplace\Demand::findBySQL($sql[0], $sql[1]);
            } catch (SearchException $e) {
                PageLayout::postError('Error', [$e->getMessage()]);
                $this->all_demands = [];
                return;
            }
        } else {

            $attribute_map = [
                'title' => 'title',
                'author' => 'auth_user_md5.username',
                'mkdate' => 'mkdate'
            ];
            $order_map = [
                'asc' => 'ASC',
                'desc' => 'DESC'
            ];
            $this->order = $order;
            $order = explode('_', $order); // split into attribute and order

            $query = "LEFT JOIN mp_tag_demand ON mp_demand.id=mp_tag_demand.demand_id LEFT JOIN mp_marketplace ON mp_demand.marketplace_id = mp_marketplace.id LEFT JOIN mp_tag ON mp_tag_demand.tag_id=mp_tag.id LEFT JOIN mp_property ON mp_property.demand_id=mp_demand.id LEFT JOIN mp_custom_property ON mp_custom_property.id=mp_property.custom_property_id";

            if ($marketplace_id) {
                $query .= " WHERE mp_marketplace.id = ? Group by mp_demand.id, mp_demand.title, mp_demand.mkdate, mp_demand.chdate, mp_demand.author_id, mp_demand.id  ORDER BY " . $attribute_map[$order[0]] . " " . $order_map[$order[1]] . " LIMIT ?";
                $this->all_demands = \Marketplace\Demand::findBySQL($query, [$marketplace_id, intval($this->limit)]);
            } else {
                $query .= " Group by mp_demand.id, mp_demand.title, mp_demand.mkdate, mp_demand.chdate, mp_demand.author_id, mp_demand.id  ORDER BY " . $attribute_map[$order[0]] . " " . $order_map[$order[1]] . " LIMIT ?";
                $this->all_demands = \Marketplace\Demand::findBySQL($query, [intval($this->limit)]);
            }


            //    $this->st = $db->fetchAll("SELECT * FROM mp_demand LEFT JOIN mp_tag_demand ON mp_demand.id=mp_tag_demand.demand_id LEFT JOIN mp_tag ON mp_tag_demand.tag_id=mp_tag.id", []);
        }
    }

    public function get_attributes_action($marketplace_id)
    {
        $db = DBManager::get();
        $custom_properties = $db->fetchAll("SELECT name, type FROM mp_custom_property WHERE marketplace_id = ?", [$marketplace_id]);
        $this->render_text('' . json_encode($custom_properties));
    }

    public function get_tags_action()
    {
        $db = DBManager::get();
        $tags = $db->fetchAll("SELECT name FROM mp_tag", []);
        $this->render_text('' . json_encode($tags));
    }

    public function save_search_action()
    {
        $search_notification = json_decode(file_get_contents('php://input'), true);
        $search_query = $search_notification["query"];
        $marketplace_id = $search_notification["marketplace_id"];
        $user_id = $GLOBALS['user']->id;
        $generator = new SqlGenerator();
        $db = DBManager::get();
        $custom_properties = $db->fetchAll("SELECT name, type FROM mp_custom_property", []);
        $categories = Category::get_categories($marketplace_id);
        try {
            $sql = $generator->generateSQL($search_query, $custom_properties, $marketplace_id,  $categories);
            $demands = \Marketplace\Demand::findBySQL($sql[0], $sql[1]);
            SearchNotification::subscribeToSearch($user_id, $search_query, $demands, $marketplace_id);
            $this->render_text('');
        } catch (SearchException $e) {
            $this->render_text('' . json_encode([error => $e->getMessage()]));
            http_response_code(500);
            return;
        }
    }
}
