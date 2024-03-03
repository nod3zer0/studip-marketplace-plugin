<?php

use Marketplace\SearchException;
use Marketplace\TagDemand;
use Marketplace\SqlGenerator;
use \Marketplace\SearchNotification;

class SearchController extends \Marketplace\Controller
{

    public function index_action($marketplace_id = '')
    {
        PageLayout::addScript($this->plugin->getPluginURL() . '/assets/autocomplete.js');
        PageLayout::addStylesheet($this->plugin->getPluginURL() . '/assets/stylesheet.css');

        $this->marketplace_id = $marketplace_id;
        Navigation::activateItem('marketplace_' . $marketplace_id . '/marketplace_search');
        PageLayout::setTitle(\Marketplace\MarketplaceModel::find($marketplace_id)->name);
        $db = DBManager::get();
        $query = Request::get('search-query');
        if ($query != '') {


            $db = DBManager::get();
            $custom_properties = $db->fetchAll("SELECT name, type FROM mp_custom_property", []);
            // $custom_properties = array_map(function ($value) {
            //     return $value['name'];
            // }, $custom_properties);


            $generator = new SqlGenerator();
            try {
                $sql = $generator->generateSQL($query, $custom_properties, $marketplace_id);

                $this->all_demands = \Marketplace\Demand::findBySQL($sql[0], $sql[1]);
            } catch (SearchException $e) {
                PageLayout::postError('Error', [$e->getMessage()]);
                $this->all_demands = [];
                return;
            }
        } else {

            $query = "LEFT JOIN mp_tag_demand ON mp_demand.id=mp_tag_demand.demand_id LEFT JOIN mp_marketplace ON mp_demand.marketplace_id = mp_marketplace.id LEFT JOIN mp_tag ON mp_tag_demand.tag_id=mp_tag.id LEFT JOIN mp_property ON mp_property.demand_id=mp_demand.id LEFT JOIN mp_custom_property ON mp_custom_property.id=mp_property.custom_property_id";

            if ($marketplace_id) {
                $query .= " WHERE mp_marketplace.id = ? Group by mp_demand.id, mp_demand.title, mp_demand.mkdate, mp_demand.chdate, mp_demand.author_id, mp_demand.id";
                $this->all_demands = \Marketplace\Demand::findBySQL($query, [$marketplace_id]);
            } else {
                $query .= " Group by mp_demand.id, mp_demand.title, mp_demand.mkdate, mp_demand.chdate, mp_demand.author_id, mp_demand.id";
                $this->all_demands = \Marketplace\Demand::findBySQL($query);
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
        try {
            $sql = $generator->generateSQL($search_query, $custom_properties, $marketplace_id);
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
